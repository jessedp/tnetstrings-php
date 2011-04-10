    <?php
require('TNetString.php');
/**
    These are encoded/dumped tnetstrings obtained from the reference implementation. after setting x to something, I simply did:
        # print dump(x)
    We're just going to run through them and make sure they are parseable
**/

$ref_samples = array(
    // x = None
    '0:~',
    // x = True 
    '4:true!',
    // x = False 
    '5:false!',
    // x = 'test'
    '4:test,',
    // x = 42
    '2:42#',    
    // x = ['x',1]
    '8:1:x,1:1#]',
    // x = {'a' : 'test', 'b' : 'd', 'test' : [1, 2] }
    '37:1:a,4:test,4:test,8:1:1#1:2#]1:b,1:d,}'
);
echo "\nCHECK DECODING REF SAMPLES\n";
echo "======================\n";
foreach($ref_samples as $sample){
    echo "checking: ". $sample."\n";
    try {
        $var = TNetString::decode($sample); 
        echo "\tGOOD\n";
    }catch (Exception $e){
        echo "\tFUCK: ".$e->getMessage()."\n";
    }
}
echo "\nCHECK ENCODING/DECODING NATIVE\n";
echo "======================\n";
/**
    These are PHP equivalents of the ref samples. They should:
        a) match the ref example after encoding
        b) have print_r's that match after re-encoding
**/
$php_samples = array(
    null,
    true,
    false,
    'test',
    42,
    array('x',1),
    array('a'=>'test', 'b'=>'d', 'test'=>array(1,2)) 
);

foreach($php_samples as $i=>$sample){
    echo "phpsample #".$i."\n";
    $orig = print_r($sample,true);
    $data = TNetString::encode($sample);
    $fucked = false;
    if ($data != $ref_samples[$i]){
        $fucked = true;
        echo "\tFUCK! - does not match ref sample\n";
    } else {
        echo "\tGOOD - tns_encode() matches ref sample\n";
    }
    list($new, $crap) = TNetString::decode($data);
    $new = print_r($new, true);
    if ($orig !== $new){
        $fucked = true;
        echo "\tFUCK! - encode/decode failure\n";
        echo $ref_samples[$i]."\n\n";
        echo "orig:". $sample."\n\n";
        echo "new:".$new."\n\n";
    } else {
        echo "\tGOOD - tns_encode()==tns_decode()\n";
    }
}
?>
