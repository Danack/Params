<?php

declare(strict_types = 1);


namespace Params\DataLocator;


class ResultStorage
{
    private $resultData = [];

    /**
     * Gets the currently processed params.
     * @return array<int|string, mixed>
     */
    public function getResultData()
    {
        return $this->resultData;
    }

    /**
     * @param array<int|string> $currentLocation
     * @param mixed $value
     */
    public function storeCurrentResult($currentLocationArray, $value)
    {
        $data = &$this->resultData;

        foreach ($currentLocationArray as $key) {
            $data = &$data[$currentLocationArray];
        }

        $data = $value;
    }

//    public function getResultByRelativeKey($currentLocationArray, $relativeKey)
//    {
//        $data = $this->resultData;
//
//        foreach ($currentLocationArray as $location) {
//            if (is_array($data) === false) {
//                return [false, null];
//            }
//
//            if (array_key_exists($location, $data) === false) {
//                return [false, null];
//            }
//
//            $data = $data[$location];
//        }
//
//        return [true, $data];
//    }
}