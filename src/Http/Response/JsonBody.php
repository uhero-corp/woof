<?php

namespace Woof\Http\Response;

use Woof\Util\DataObject;
use Woof\Util\RawDataObject;

class JsonBody implements Body
{
    /**
     * @var DataObject
     */
    private $data;

    /**
     * json_encode() の引数として指定されるオプション
     *
     * @var int
     */
    private $encodeOptions;

    /**
     * @var string
     */
    private $output;

    /**
     * 指定された値を JSON として取り扱う JsonBody オブジェクトを生成します。
     *
     * @param DataObject|array $data
     * @param int $encodeOptions json_encode() のオプション
     */
    public function __construct($data, int $encodeOptions = 0)
    {
        $dataObject          = ($data instanceof DataObject) ? $data : new RawDataObject($data);
        $this->data          = $dataObject;
        $this->encodeOptions = $encodeOptions;
        $this->output        = json_encode($dataObject->toValue(), $encodeOptions);
    }

    /**
     * @return DataObject
     */
    public function getData(): DataObject
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getEncodeOptions(): int
    {
        return $this->encodeOptions;
    }

    /**
     * @return string
     */
    public function getOutput(): string
    {
        return $this->output;
    }

    /**
     * @return bool
     */
    public function sendOutput(): bool
    {
        echo $this->output;
        return true;
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return "application/json";
    }

    /**
     * @return int
     */
    public function getContentLength(): int
    {
        return strlen($this->output);
    }
}
