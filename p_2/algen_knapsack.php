<?php

class Parameters{
    const FILE_NAME = 'products.txt';
    const COLUMNS = ['item', 'price'];
    const POPULATION_SIZE = 6;
    const BUDGET = 15000;
}

class Catalogue{
    function createProductColumn($listOfRawProduct){
        foreach (array_keys($listOfRawProduct) as $listOfRawProductKey) {
            $listOfRawProduct[Parameters::COLUMNS[$listOfRawProductKey]] = $listOfRawProduct[$listOfRawProductKey];
            unset($listOfRawProduct[$listOfRawProductKey]);
        }
        return $listOfRawProduct;
    }

    function product(){
        $collectionOfListProduct = [];

        $raw_data = file(Parameters::FILE_NAME);
        foreach ($raw_data as $listOfRawProduct) {
            $collectionOfListProduct[] = $this->createProductColumn(explode(",", $listOfRawProduct));
        }
        
        return $collectionOfListProduct;
    }
}


class Individu{
    function countNumberOfGen(){
        $catalogue = new Catalogue();
        return count($catalogue->product());
    }
    
    function createRandomIndividu(){
        for ($i = 0; $i <= $this->countNumberOfGen() - 1; $i++) {
            $ret[] = rand(0, 1);
        }
        return $ret;
    }
}


class Population{

    function createRandomPopulation(){
        $individu = new Individu;
        for ($i = 0; $i <= Parameters::POPULATION_SIZE - 1; $i++) {
            $ret[] = $individu->createRandomIndividu();
        }
        return $ret;
    }
}


class Fitness{
    function selectingItem($individu){
        $catalogue = new Catalogue;
        $ret = [];
        foreach ($individu as $individuKey => $binaryGen) {
            if ($binaryGen === 1) {
                $ret[] = [
                    'selectedKey' => $individuKey,
                    'SelectedPrice' => $catalogue->product()[$individuKey]['price']
                ];
            }
        }
        return $ret;
    }

    function calculateValue($individu){
        print_r($this->selectingItem($individu));
        return array_sum(array_column($this->selectingItem($individu), 'SelectedPrice'));
    }


    function countSelectedItems($individu)
    {
        return count($this->selectingItem($individu));
    }

    function searchBestIndividu($fits, $maxItem, $numberOfIndividuMaxItem){
        if ($numberOfIndividuMaxItem === 1) {
            $index = array_search($maxItem, array_column($fits, 'numberOfSelectedItem'));
            print_r($fits[$index]);
        } else {
            foreach ($fits as $key => $val) {
                if ($val['numberOfSelectedItem'] === $maxItem) {
                    echo $key . ' ' . $val['fitnessValue'] . '<br>';
                    $rets[] = [
                        'individuKey' => $key,
                        'fitnessvalue' => $val['fitnessValue']
                    ];
                }
            }

            if (count(array_unique(array_column($rets, 'fitnessValue'))) === 1) {
                $index = rand(0, count($rets) - 1);
            } else {
                $max = max(array_column($rets, 'fitnessvalue'));

                $index = array_search($max, array_column($rets, 'fitnessvalue'));
            }
            print_r($rets[$index]);
        }

    }

    function isFound($fits){
        $countMaxItems = array_count_values(array_column($fits, 'numberOfSelectedItem'));

        $maxItem =  max(array_keys($countMaxItems));

        $numberOfIndividuMaxItem = $countMaxItems[$maxItem];

        $this->searchBestIndividu($fits, $maxItem, $numberOfIndividuMaxItem);
    }

    function isFit($fitnessValue){
        if ($fitnessValue <= Parameters::BUDGET) {
            return TRUE;
        }
    }

    //memasukkan barang kedalam keranjang
    function fitnessEvaluation($population){
        $catalogue = new Catalogue;
        
        foreach ($population as $listOfIndividuKey => $listOfIndividu) {
            
            echo 'individu - ' . $listOfIndividuKey . '<br>';

            foreach ($listOfIndividu as $individuKey => $binaryGen) {
                echo $binaryGen . '&nbsp;&nbsp';
                print_r($catalogue->product()[$individuKey]);
                echo '<br>';
            }
            
            $fitnessValue = $this->calculateValue($listOfIndividu);
            echo 'Fitness Value : ' . $fitnessValue;
            echo '<br>';
            echo 'Items : ' . $this->countSelectedItems($listOfIndividu);
            echo '<br>';
            
            if ($this->isFit($fitnessValue)) {
                // echo '(fit)';
                $fits[] = [
                    'selectedIndividuKey' => $listOfIndividuKey,
                    'numberOfSelectedItem' => $this->countSelectedItems($listOfIndividu),
                    'fitnessValue' => $fitnessValue
                ];
                var_dump($fits);
            } else {
                echo '(not fit)';
            }
            
            echo '<br>' . '<br>';
        }

        echo '<br>';
        $this->isFound($fits);
    }
}

$initialPopulation = new Population;
$population = $initialPopulation->createRandomPopulation();
$fitness = new Fitness;
$fitness->fitnessEvaluation($population);


