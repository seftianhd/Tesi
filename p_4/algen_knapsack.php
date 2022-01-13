<?php

class Parameters{
    const FILE_NAME = 'products.txt';
    const COLUMNS = ['item', 'price'];
    const POPULATION_SIZE = 6;
    const BUDGET = 15000;
    const STOPPING_VALUE = 8000;
    const CROSSOVER_RATE = 0.9;
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

class Crossover{
    public $populations;
    function __construct($populations){
        $this->populations = $populations;
    }

    function randomZeroToOne(){
        return (float) rand() / (float) getrandmax();
    }

    function generateCrossover(){
        for ($i = 0; $i <= Parameters::POPULATION_SIZE - 1; $i++) {
            $randomZeroToOne = $this->randomZeroToOne();
            
            if ($randomZeroToOne < Parameters::CROSSOVER_RATE) {
                $parents[$i] = $randomZeroToOne;
            }
        }

        foreach (array_keys($parents) as $key) {
            
            foreach (array_keys($parents) as $subkey) {
                if ($key !== $subkey) {
                    $ret[] = [$key, $subkey];
                }
            }
            
            array_shift($parents);
        }

        echo 'ret : <p>';
        print_r($ret);
        
        exit;
        return $ret;
        // echo '<br>';
        // print_r($parents);
    }

    function offspring($parent1, $parent2, $cutPointIndex, $offspring){
        $lengthOfGen = new Individu;
        
        if ($offspring === 1) {
            
            for ($i = 0; $i <= $lengthOfGen->countNumberOfGen() - 1; $i++) {
                
                if ($i <= $cutPointIndex) {
                    $ret[] = $parent1[$i];
                }

                if ($i > $cutPointIndex) {
                    $ret[] = $parent2[$i];
                }
            }

        }

        if ($offspring === 2) {
            
            for ($i = 0; $i <= $lengthOfGen->countNumberOfGen() - 1; $i++) {
                if ($i <= $cutPointIndex) {
                    $ret[] = $parent2[$i];
                }

                if ($i > $cutPointIndex) {
                    $ret[] = $parent1[$i];
                }
            }

        }

        return $ret;
    }

    function cutPointRandom(){
        $lengthOfGen = new Individu;
        return rand(0, $lengthOfGen->countNumberOfGen() - 1);
    }

    function crossover(){
        $cutPointIndex = $this->cutPointRandom();
        echo 'CutpointIndex : ' . $cutPointIndex;
        
        foreach ($this->generateCrossover() as $listOfCrossover) {
            $parent1 = $this->populations[$listOfCrossover[0]];
            $parent2 = $this->populations[$listOfCrossover[1]];
            echo '<br><br> Parents : <br>';
            
            foreach ($parent1 as $gen) {
                echo $gen;
            }

            echo '><';
            
            foreach ($parent2 as $gen) {
                echo $gen;
            }
            
            echo '<br>';
            echo 'Offspring : ';
            
            $offspring1 = $this->offspring($parent1, $parent2, $cutPointIndex, 1);
            $offspring2 = $this->offspring($parent1, $parent2, $cutPointIndex, 2);
            // print_r($offspring1);
            // echo '<br>';
            // print_r($offspring2);
            
            foreach ($offspring1 as $gen) {
                echo $gen;
            }
            
            foreach ($offspring2 as $gen) {
                echo $gen;
            }
        }
    }
}

class Randomizer
{
    static function getRandomIndexOfGen()
    {
        return rand(0, (new Individu())->countNumberOfGen() - 1);
    }

    static function getRandomIndexOfIndividu()
    {
        return rand(0, Parameters::POPULATION_SIZE - 1);
    }
}

class Mutation{
    function __construct($population){
        $this->population = $population;
    }

    function calculateMutationRate(){
        return 1 / (new Individu())->countNumberOfGen();
    }

    function calculateNumOfMutation(){
        return round($this->calculateMutationRate() * Parameters::POPULATION_SIZE);
    }

    function isMutation(){
        if ($this->calculateNumOfMutation() > 0) {
            return TRUE;
        }
    }

    function generateMutation($valueOfGen){
        if ($valueOfGen === 0) {
            return 1;
        } else {
            return 0;
        }
    }

    function mutation(){
        
        if ($this->isMutation()) {
            for ($i = 0; $i <= $this->calculateNumOfMutation() - 1; $i++) {
                
                $indexOfIndividu = Randomizer::getRandomIndexOfIndividu();
                $indexOfGen = Randomizer::getRandomIndexOfGen();
                $selectedIndividu = $this->population[$indexOfIndividu];
                
                echo '<br>';
                echo 'before mutation: <br>';
                print_r($selectedIndividu);
                echo '<br>';
                
                $valueOfGen = $selectedIndividu[$indexOfGen];
                $mutatedGen = $this->generateMutation($valueOfGen);
                $selectedIndividu[$indexOfGen] = $mutatedGen;
                
                echo 'after mutation : <br>';
                print_r($selectedIndividu);
                $ret[] = $selectedIndividu;
            }

            return $ret;
        }
        
    }
}

// $katalog = new Catalogue;
// var_dump($katalog->product());

$initialPopulation = new Population;
$population = $initialPopulation->createRandomPopulation();

// $fitness = new Fitness;
// $fitness->fitnessEvaluation($population);

$crossover = new Crossover($population);
$crossoverOffspring = $crossover->crossover();

echo 'Crossover Offspring: <br>';
print_r($crossoverOffspring);

echo '<br><br>';
$mutation = new Mutation($population);

if ($mutation->mutation()) {
    
    $mutationOffsprings = $mutation->mutation();
    echo 'Mutation Offspring: <br>';
    print_r($mutationOffsprings);
    echo '<br>';
    
    foreach ($mutationOffsprings as $mutationOffspring) {
        $crossoverOffsprings[] = $mutationOffspring;
    }
}

echo 'Mutation Offspring: <br>';
print_r($crossoverOffspring);

// $individu = new Individu;
// var_dump($individu->createRandomIndividu());
