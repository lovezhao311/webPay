<?php
namespace LuffyZhao\Library;

/**
 * 文档地址
 * https://doc.open.alipay.com/docs/doc.htm?spm=a219a.7629140.0.0.hL7ca4&treeId=60&articleId=104741&docType=1
 * https://doc.open.alipay.com/docs/doc.htm?spm=a219a.7629140.0.0.kNStrq&treeId=62&articleId=104741&docType=1
 */
abstract class Payment
{
    protected $config = [];

    protected $data = [];

    /**
     * 构造函数
     * @param [type] $config [description]
     */
    public function __construct()
    {

    }

    /**
     * 设置配置文件
     * @param [type] $config [description]
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    public function setOrder(array $params)
    {
        $this->data = $params;
    }

    abstract public function create();

}
