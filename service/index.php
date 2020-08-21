<?php
//Get input from Graphql
$textToEval = file_get_contents('php://input');

//Modify phraseDelimiters to add or remove any string that delimits phrases 
$phraseDelimiters = array('.',';','!','?');

//Override input and test alice-in-wonderland.txt
$textToEval = file_get_contents('alice-in-wonderland.txt');
 
//Define a storage [Phrase][count]
$phraseHash = array();

//Normalize input text
// - Break input into sentences
$delimiter = ':::';
$textToEval = strtolower($textToEval);
$textToEval = str_replace($phraseDelimiters, $delimiter, $textToEval);

// - Cleanup white space
$textToEval =  trim(preg_replace('/\s+/', ' ',$textToEval));
$textToEval = preg_replace("/[^a-z0-9: ]/", '', $textToEval);

// - Remove Last delimiter and extra spaces
$textToEval = trim($textToEval, ': ');

//Create an array of sentences and look for phrases
$sentenceArray = explode($delimiter, $textToEval);

foreach($sentenceArray as $sentence){
    //Remove white space that may tail a sentence
    $sentence =  trim(preg_replace('/\s+/', ' ',$sentence));
    
    //Discover and count phrases 
    $words = explode(' ', $sentence);
    $numberOfWords = count($words);
    if($numberOfWords > 2){
        for ($i = 0; $i < $numberOfWords-2; $i++) {
            $phrase = trim($words[$i] . ' ' . $words[$i+1] . ' ' . $words[$i+2]);
            @$phraseHash[$phrase]++;// = empty($phraseHash[$phrase]) ? 1 : $phraseHash[$phrase]++;
        }
    }
}

//Sort from most to least
arsort($phraseHash);

//Only output at most the top 100
$phraseHash = array_slice($phraseHash, 0, 99);

//Output -needs to be formated [words: String, count: Int]
$output = array();
foreach($phraseHash as $word => $count){
    $output[] = ['words' => $word, 'count' => $count];
}
echo json_encode($output);
