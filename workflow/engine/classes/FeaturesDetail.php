<?php

class FeaturesDetail
{
    public $featureName;
    public $description = null;
    public $enabled = false;
    public $workspaces = null;

    /**
     * This function is the constructor of the featuresDetail class
     *
     * @param string $featureName
     * @param string $name
     * @param string $description
     * @return void
     */
    public function __construct ($featureName, $description = '')
    {
        $this->featureName = $featureName;
        $this->description = $description;
    }
}
