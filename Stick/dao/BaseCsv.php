<?php
namespace Stick\dao;

class BaseCsv extends \Stick\AbstractObject
{
    protected $path;
    protected $delimiter;
    protected $data;
    protected $header;
    protected $header_flag;
    protected $header_set;

    /**
     * 初期化
     *
     * @param string $path
     * @param boolean $flag
     * @param string $delimiter
     *
     * @return obj $this
     */
    public function initialize($path = null, $flag = false, $delimiter = ',')
    {
        $this->path = $path;
        $this->delimiter = $delimiter;
        $this->data = null;
        $this->header = array();
        $this->header_flag = $flag;
        $this->header_set = null;
        if ($this->path !== null) {
            if (is_readable($this->path)) {
                $this->readCsv();
            }
        }
        return $this;
    }

    /**
     * 値の取得
     *
     * @param mixed num
     *
     * @return mixed
     */
    public function getValue($num = null)
    {
        if ($num === null) {
            return $this->data;
        } elseif (isset($this->data[$num])) {
            return $this->data[$num];
        } else {
            return false;
        }
    }

    /**
     * Csvファイルに値を書き込む（OverWrite）
     *
     * @param array
     *
     * @return int or false
     */
    public function setValue(array $data_array)
    {
        $result = $this->writeCsv($data_array);
        if ($result !== false) {
            $this->initialize($this->path, $this->header_flag, $this->delimiter);
        }
        return $result;
    }

    /**
     * setValue時に使用するヘッダーを指定する
     *
     * @param array
     *
     * @return obj $this
     */
    public function setHeader(array $header_set)
    {
        $this->header_set = $header_set;
        $this->header_flag = true;
        return $this;
    }

    protected function readCsv()
    {
        $data_all_str = file_get_contents($this->path);
        $data_all_array = explode("\n", $data_all_str);
        $this->data = array();
        if ($this->header_flag) {
            $header_str = array_shift($data_all_array);
            $this->header = explode($this->delimiter, $header_str);
        }
        foreach ($data_all_array as $data_str) {
            if ($data_str === '') {
                continue;
            }
            $data_array = explode($this->delimiter, $data_str);
            if ($this->header_flag) {
                $keyed_data_array = array();
                foreach ($this->header as $n => $key) {
                    if (isset($data_array[$n])) {
                        $keyed_data_array[$key] = $data_array[$n];
                    } else {
                        $keyed_data_array[$key] = null;
                    }
                }
                $this->data[] = $keyed_data_array;
            } else {
                $this->data[] = $data_array;
            }
        }
    }

    protected function writeCsv(array $data_array)
    {
        $set_data_str = '';

        if ($this->header_set !== null) {
            $set_data_str .= implode($this->delimiter, $this->header_set) . "\n";
        } elseif ($this->header_flag) {
            $set_data_str .= implode($this->delimiter, $this->header) . "\n";
        }

        foreach ($data_array as $item) {
            if (is_array($item)) {
                if ($this->header_set !== null || $this->header_flag) {
                    $set_line_str = '';
                    $first_flag = true;
                    if ($this->header_set !== null) {
                        $header_array = $this->header_set;
                    } else {
                        $header_array = $this->header;
                    }
                    foreach ($header_array as $key) {
                        if ($first_flag) {
                            $first_flag = false;
                        } else {
                            $set_line_str .= $this->delimiter;
                        }
                        if (isset($item[$key])) {
                            $set_line_str .= $item[$key];
                        }
                    }
                    $set_data_str .= $set_line_str . "\n";
                } else {
                    $set_data_str .= implode($this->delimiter, $item) . "\n";
                }
            } else {
                $set_data_str .= $item . "\n";
            }
        }

        return file_put_contents($this->path, $set_data_str, LOCK_EX);
    }
}
