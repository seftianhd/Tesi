<?php

class catalogue{
    function createProductColum($columns, $listofRawProduct){
        foreach(array_keys($listofRawProduct) as $listofRawProductKey){
            $listofRawProduct[$columns[$listofRawProductKey]]= $listofRawProduct[$listofRawProductKey];
            unset($listofRawProduct[$listofRawProductKey]);
        }
        return $listofRawProduct;

    }


    function products($parameter){
        $koleksiListProdak = [];

       $raw_data= file($parameter['file_name']);
       foreach ($raw_data as $listofRawProduct){
           $koleksiListProdak[]= $this->createProductColum($parameter['columns'], explode(",",$listofRawProduct));
       }

       return [    
        'product' => $koleksiListProdak,
        'gen_length' => count($koleksiListProdak)
    ];

    }
}



class PopulationGenerator{
    function createIndividu($parameter){
        $catlogue = new catalogue;
        $lengthgen = $catlogue->products($parameter)['gen_length'];
        for ($i = 0; $i <= $lengthgen-1; $i++){
            $ret[] = rand(0,1);
        }

            return $ret;
    }

    function createPopulation($parameter){
        for ($i = 0; $i <= $parameter['population_size']; $i++){
           $ret[] = $this->createIndividu($parameter);
        }

        foreach ($ret as $key => $val){
            print_r($val);
            echo '<br>';
        }
    }

}

$parameter = [

    'file_name' => 'products.txt', 
    'columns'   => ['item', 'price'], 
    'population_size' => 10
];

$ktalog = new catalogue;
$ktalog->products($parameter);

$initalPopulation = new PopulationGenerator;
$initalPopulation->createPopulation($parameter);


?>