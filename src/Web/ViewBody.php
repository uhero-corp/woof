<?php

namespace Woof\Web;

use Woof\Http\Response\Body;
use Woof\Resources;
use Woof\Web\Context;

class ViewBody implements Body
{
    /**
     * @var View
     */
    private $view;

    /**
     * @var Resources
     */
    private $resources;

    /**
     * @var WebContext
     */
    private $context;

    /**
     *
     * @var string
     */
    private $output;

    /**
     * @param View $view
     * @param Resources $resources
     * @param Context $context
     */
    public function __construct(View $view, Resources $resources, Context $context)
    {
        $this->view      = $view;
        $this->resources = $resources;
        $this->context   = $context;
    }

    /**
     * @return View
     */
    public function getView(): View
    {
        return $this->view;
    }

    /**
     * @return int
     */
    public function getContentLength(): int
    {
        return strlen($this->getOutput());
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->view->getContentType();
    }

    /**
     * @return string
     */
    public function getOutput(): string
    {
        if ($this->output === null) {
            $this->output = $this->view->render($this->resources, $this->context);
        }
        return $this->output;
    }

    /**
     * @return bool
     */
    public function sendOutput(): bool
    {
        echo $this->getOutput();
        return true;
    }
}
