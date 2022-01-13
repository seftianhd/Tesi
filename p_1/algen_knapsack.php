<?php

class Catalogue{
    function createProductColumn($columns, $listOfRawProduct){
        foreach (array_keys($listOfRawProduct) as $listOfRawProductKey) {
            $listOfRawProduct[] = $listOfRawProduct[$listOfRawProductKey];
            unset($listOfRawProduct[$listOfRawProductKey]);
        }
        return $listOfRawProduct;
    }

    function product($parameters){
        $collectionOfListProduct = [];

        $raw_data = file($parameters['file_name']);
        foreach ($raw_data as $listOfRawProduct) {
            $collectionOfListProduct[] = $this->createProductColumn($parameters['columns'], explode(",", $listOfRawProduct));
        }

        return [
            'product' => $collectionOfListProduct,
            'gen_length' => count($collectionOfListProduct)
        ];
    }
}


class PopulationGenerator{
    
    function createIndividu($parameters){
        $catalogue = new Catalogue;
        $lengthOfGen = $catalogue->product($parameters)['gen_length'];
        for ($i = 0; $i <= $lengthOfGen - 1; $i++) {
            $ret[] = rand(0, 1);
        }

        return $ret;
    }

    function createPopulation($parameters){
        for ($i = 0; $i <= $parameters['population_size']; $i++) {
            $ret[] = $this->createIndividu($parameters);
        }

        foreach ($ret as $key => $val) {
            print_r($val);
        }
    }
}


$parameters = [
    'file_name' => 'products.txt',
    'columns' => ['item', 'price'],
    'population_size' => 10
];

$katalog = new Catalogue;

$initialPopulation = new PopulationGenerator;
$initialPopulation->createPopulation($parameters);