<?php
/**
 * sJo
 *
 * PHP version 5
 *
 * @package  sJo
 * @author   Jonathan Sahm <contact@johnstyle.fr>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/johnstyle/sjo.git
 */

namespace sJo\Libraries;

class Csv
{
    protected $file;
    protected $fileMv;
    protected $handle;
    protected $rawHeader;
    protected $rawLine;
    protected $rawLines;
    protected $header;
    protected $line;
    protected $lines;
    protected $options;

    protected $separator = ';';
    protected $container = '"';
    protected $max_size = 1024;

    public function __construct ($options = array())
    {
        /** @formatter:off */
        $this->options = array_merge(array(
            'hasHeader'     => true,
            'lineStart'     => 0,
            'isArray'       => false,
            'orderby'       => false,
            'order'         => SORT_ASC,
            'limit'         => 0,
            'start'         => 0,
            'filter'        => false,
            'separator'     => $this->separator,
            'container'     => $this->container,
            'max_size'      => $this->max_size
        ), $options);
        /** @formatter:on */

        $this->separator = $this->options['separator'];
        $this->container = $this->options['container'];
        $this->max_size = $this->options['max_size'];
    }

    /**
     * Destruction de la connexion avec le fichier
     *
     * @return void
     */
    public function __destruct ()
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }

    /**
     * Création du fichier
     *
     * @param string $file chemin du fichier
     * @return object
     */
    public function create ($file)
    {
        $this->fileMv = $file;
        $this->file = SJO_ROOT_TMP . '/' . md5($file);
        $this->delete();
        return $this->handle();
    }

    /**
     * Ouverture du fichier
     *
     * @param string $file chemin du fichier
     * @return object
     */
    public function open ($file)
    {
        $this->file = $file;
        return $this->handle();
    }

    /**
     * Rechargement du fichier avec récupération du header
     *
     * @return object
     */
    public function handle ()
    {
        if ($this->file) {
            if (!file_exists($this->file)) {
                touch($this->file);
            }
            if ($this->handle = fopen($this->file, 'r+')) {
                if ($this->options['hasHeader']) {
                    if ($this->options['lineStart']) {
                        for ($i = 0; $i < ($this->options['lineStart'] - 1); $i++) {
                            stream_get_line($this->handle, 0, "\n");
                        }
                    }
                    $this->rawHeader = stream_get_line($this->handle, 0, "\n");
                    $this->header = self::fromRaw($this->rawHeader);
                    if ($this->header) {
                        foreach ($this->header as $i => &$header) {
                            if (empty($header)) {
                                $header = 'column' . ($i + 1);
                            }
                        }
                    }
                }
            }
        }
        return $this->handle;
    }

    /**
     * Parcours de chaque ligne du fichier
     *
     * @return boolean
     */
    public function loop ()
    {
        if (is_resource($this->handle)) {
            $feof = !feof($this->handle);
            $this->line = false;
            if ($this->rawLine = stream_get_line($this->handle, 0, "\n")) {
                return $feof;
            }
        }
        return false;
    }

    /**
     * Retourne l'entête
     *
     * @return object
     */
    public function getHeader ()
    {
        return $this->header;
    }    

    /**
     * Retourne la ligne courante
     *
     * @return object
     */
    public function getLine ()
    {
        return $this->line;
    }

    /**
     * Retourne le groupe de lignes courantes
     *
     * @return object
     */
    public function getLines ()
    {
        return $this->lines;
    }

    /**
     * Retourne la ligne courante au format CSV
     *
     * @return string
     */
    public function getRawLine ()
    {
        return $this->rawLine;
    }

    /**
     * Retourne le groupe de lignes courantes au format CSV
     *
     * @return string
     */
    public function getRawLines ()
    {
        return $this->rawLines;
    }

    /**
     * Transforme la ligne CSV en objet
     *
     * @return Csv
     */
    public function toObject ()
    {
        if (is_resource($this->handle)) {
            $data = self::fromRaw($this->rawLine);
            if ($this->header) {
                foreach ($this->header as $i => $header) {
                    if ($this->options['isArray']) {
                        if (!$this->line) {
                            $this->line = array();
                        }
                        if (isset($data[$i])) {
                            $this->line[$header] = $data[$i];
                        } else {
                            $this->line[$header] = '';
                        }
                    } else {
                        if (!$this->line) {
                            $this->line = new \stdClass ();
                        }
                        if (isset($data[$i])) {
                            $this->line->{$header} = $data[$i];
                        } else {
                            $this->line->{$header} = '';
                        }
                    }
                }
            } else {
                foreach ($data as $i => $value) {
                    if ($this->options['isArray']) {
                        if (!$this->line) {
                            $this->line = array();
                        }
                        $this->line[$i] = $value;
                    } else {
                        if (!$this->line) {
                            $this->line = new \stdClass ();
                        }
                        $this->line->{$i} = $value;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Ajoute plusieurs lignes de données
     *
     * @param $items
     * @return void
     */
    public function addLines ($items)
    {
        $this->lines = false;
        $this->rawLines = false;
        if (is_resource($this->handle)) {
            if ($items) {
                foreach ($items as $item) {
                    if ($this->addLine($item)) {
                        $this->lines[] = $this->line;
                        $this->rawLines .= self::toRaw($this->line);
                    }
                }
            }
        }
    }

    /**
     * Ajoute une ligne de données
     *
     * @param $item
     * @return object
     */
    public function addLine ($item)
    {
        $this->line = false;
        $this->rawLine = false;
        if (is_resource($this->handle)) {
            if ($item) {

                $item = (array)$item;

                /** Ajout des nouveaux éléments au header */
                if ($this->options['hasHeader']) {
                    $headerType = 'string';
                    if (!$this->header) {
                        $this->header = array();
                        foreach ($item as $name => $value) {
                            if (is_int($name)) {
                                $headerType = 'int';
                            }
                            if (!in_array($name, $this->header)) {
                                $this->header[] = $name;
                            }
                        }
                        $this->rawHeader = self::toRaw($this->header, false);
                    } else {
                        foreach ($item as $name => $value) {
                            if (is_int($name)) {
                                $headerType = 'int';
                            } elseif (!in_array($name, $this->header)) {
                                $this->header[] = $name;
                            }
                        }
                        $this->rawHeader = self::toRaw($this->header, false);
                    }
                    $this->prepend($this->rawHeader);

                    /** Constitution de la ligne */
                    $this->line = new \stdClass ();
                    foreach ($this->header as $i => $header) {
                        switch($headerType) {
                            case 'string' :
                                if (isset($item[$header])) {
                                    $this->line->{$header} = is_array($item[$header]) ? implode('\n', $item[$header]) : $item[$header];
                                } else {
                                    $this->line->{$header} = '';
                                }
                                break;
                            case 'int' :
                                if (isset($item[$i])) {
                                    $this->line->{$header} = is_array($item[$i]) ? implode('\n', $item[$i]) : $item[$i];
                                } else {
                                    $this->line->{$header} = '';
                                }
                                break;
                        }
                    }
                } else {
                    $this->line = $item;
                }
                $this->rawLine = self::toRaw($this->line);
                $this->append($this->rawLine);
            }
        }
        return $this->line;
    }

    public function prepend ($str)
    {
        fseek($this->handle, 0);
        fwrite($this->handle, $str . str_pad(" ", $this->max_size - strlen($str)) . "\n");
    }

    public function append ($str)
    {
        fseek($this->handle, 0, SEEK_END);
        fwrite($this->handle, $str);
    }

    /**
     * Headers du fichier CSV
     *
     * @param string $filename Nom du fichier à envoyer au navigateur
     * @return void
     */
    public static function headers ($filename)
    {
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Description: File Transfer');
        header('Content-Type: text/csv; charset: UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Pragma: public');
    }

    /**
     * Affiche le CSV
     *
     * @param $filename
     * @return void
     */
    public function display ($filename)
    {
        self::headers($filename);
        readfile($this->file);
    }

    /**
     * Supprime le fichier
     *
     * @return void
     */
    public function delete ()
    {
        if ($this->fileMv) {
            $file = $this->fileMv;
        } else {
            $file = $this->file;
        }

        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * Ferme proprement le fichier
     *
     * @return void
     */
    public function close ()
    {
        if ($this->fileMv) {
            rename($this->file, $this->fileMv);
        }

        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }

    /**
     * Converti une ligne CSV en tableau de données
     *
     * @param $line
     * @return array
     */
    public function fromRaw ($line)
    {
        $data = false;
        $line = trim($line);
        if (!empty($line)) {
            if ($this->container) {
                $first = strpos($line, $this->container) + 1;
                $last = strrpos($line, $this->container) - 1;
                $line = substr($line, $first, $last);
            }
            $items = explode($this->container . $this->separator . $this->container, $line);
            foreach ($items as $item) {
                $data[] = $item;
            }
        }
        return $data;
    }

    /**
     * Converti un tableau de données en ligne CSV
     *
     * @param $line
     * @param string $break
     * @return array
     */
    public function toRaw ($line, $break = "\n")
    {
        $rawLine = false;
        if ($line) {
            foreach ($line as $val) {
                $rawLine[] = $this->container . $val . $this->container;
            }
            $rawLine = implode($this->separator, $rawLine) . $break;
        }
        return $rawLine;
    }

    /**
     * Insertion rapide dans un fichier de log
     */
    public static function log ($file, $line)
    {
        $csv = new self ();
        $csv->open($file);
        $csv->addLine($line);
    } 

    public static function arrayToRaw ($line, $break = "\n")
    {
        $csv = new self ();
        return $csv->toRaw($line, $break);
    } 

    public static function arrayFromRaw ($lines, $options = array())
    {
        $data = array();
        $lines = explode("\n", $lines);
        $csv = new self ($options);
        foreach($lines as $line) {
            $data[] = $csv->fromRaw($line);
        }        
        return $data;
    } 
}
