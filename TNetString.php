<?php
class TNetString {
    public static function encode($data){
        if ( is_float($data) || is_int($data) ){
            $data = (string)$data;
            return sprintf('%d:%s#', strlen($data), $data);
        } elseif( is_string($data) ){
            return sprintf('%d:%s,', strlen($data), $data);
        } elseif( is_array($data) ){
            $ak = array_pop(array_keys($data));
            if (is_int($ak)){
                $parts = array();
                foreach($data as $v){
                    $parts[] = self::encode($v);
                }
                $payload = implode('',$parts);
                return sprintf('%d:%s]',strlen($payload),$payload);
            } else {
                $parts = array();
                foreach($data as $k=>$v){
                    $parts[] = self::encode($k);
                    $parts[] = self::encode($v);
                }
                $payload = implode('',$parts);
                return sprintf('%d:%s}',strlen($payload),$payload);
            }
        } elseif($data === null){
            return '0:~';
        } elseif(is_bool($data)){
            $repr = $data?'true':'false';
            return sprintf('%d:%s!',strlen($repr),$repr);
        }
        throw new Exception("Can't serialize stuff that's ".gettype($data));
    }

    public static function decode($data){
        list($payload, $payload_type, $remain) = self::decodePayload($data);
        switch($payload_type){
            case '#': $value = (int)$payload; break;
            case '}': $value = self::decode_dict($payload); break;
            case ']': $value = self::decode_list($payload); break;
            case '!': $value = $payload==='true'; break;
            case '~': 
                if (strlen($payload)!=0){
                    throw new Exception("Payload must be 0 length for null.");
                }
                $value = null;
                break;
            case ',': $value = (string)$payload;break;
            default:
                throw new Exception("Invalid payload type: ".$payload_type);
        }
        return array($value, $remain);    
    }
    private static function decodePayload($data){
        if (empty($data)){
            throw new Exception("Invalid data to parse, it's empty.");
        }
        list($len, $extra) = explode(':',$data,2);
        $payload = substr($extra,0,$len);
        $extra = substr($extra,$len);
        if (!$extra){
            throw new Exception(sprintf("No payload type: %s, %s.",$payload,$extra));
        }
        $payload_type = $extra[0];
        $remain = substr($extra,1);
        if (strlen($payload)!=$len){
            throw new Exception(sprintf("Data is wrong length %d vs %d",strlen($payload),$len));
        }
        return array($payload, $payload_type, $remain);
    }

    private static function decode_list($data){
        if (strlen($data) == 0) return array();

        $result = array();
        list($value, $extra) = self::decode($data);
        $result[] = $value;

        while ($extra){
            list($value, $extra) = self::decode($extra);
            $result[] = $value;
        }
        return $result;
    }

    private static function decode_pair($data){
        list($key, $extra) = self::decode($data);
        if (!$extra){
            throw new Exception("Unbalanced dictionary store.");
        }
        list($value, $extra) = self::decode($extra);
        if (!$value){
            throw new Exception("Got an invalid value, null not allowed.");
        }

        return array($key, $value, $extra);
    }


    private static function decode_dict($data){
        if (strlen($data) == 0) return array();
        
        list($key, $value, $extra) = self::decode_pair($data);
        $result = array($key=>$value);

        while ($extra){
            list($key, $value, $extra) = self::decode_pair($extra);
            $result[$key] = $value;
        }
        return $result;
    }
}
?>
