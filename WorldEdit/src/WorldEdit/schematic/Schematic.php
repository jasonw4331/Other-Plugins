<?php

namespace WorldEdit\schematic;

class Schematic {

    /** @var array */
    private $blocks = [];

    /** @var int */
    private $width;

    /** @var int */
    private $length;

    /** @var int */
    private $height;

    /**
     * Schematic constructor.
     *
     * @param array $blocks
     * @param $width
     * @param $length
     * @param $height
     */
    public function __construct($blocks, $width, $length, $height) {
        $this->blocks = $blocks;
        $this->width = (int) $width;
        $this->length = (int) $length;
        $this->height = (int) $height;
    }

    /**
     * @return array
     */
    public function getBlocks() {
        return $this->blocks;
    }

    /**
     * @return int
     */
    public function getWidth() {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getLength() {
        return $this->length;
    }

    /**
     * @return int
     */
    public function getHeight() {
        return $this->height;
    }

    /**
     * @param array $blocks
     */
    public function setBlocks($blocks) {
        $this->blocks = $blocks;
    }

    /**
     * @param int $width
     */
    public function setWidth($width) {
        $this->width = $width;
    }

    /**
     * @param int $length
     */
    public function setLength($length) {
        $this->length = $length;
    }

    /**
     * @param int $height
     */
    public function setHeight($height) {
        $this->height = $height;
    }

}