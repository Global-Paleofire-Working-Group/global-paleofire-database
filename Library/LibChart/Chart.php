<?php

abstract class Chart {

    private $_chart_picture;
    private $_chart_data;

    public function __construct() {
        $this->_chart_data = new pData();
    }

    public function render($path_image) {
        $this->_chart_picture->Render($path_image);
    }

    public function setImageParameters($XSize, $YSize) {
        if ($this->_chart_data->containsData()) {
            $this->_chart_picture = new pImage($XSize, $YSize, $this->_chart_data);

        }else
            {
            throw new Exception("Chart : The image can be set only if data are set");
        }
    }

}
