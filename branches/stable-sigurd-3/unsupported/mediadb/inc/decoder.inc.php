<?php

/*
 * Based on kittycode.js by Dustin Sallings dustin@spy.net
 *
 * Copyright (c) 2000  Dustin Sallings dustin@spy.net
 * Any work derived from this code must retain the above copyright.
 * Please send all modifications of this script to me.
 * http://bleu.west.spy.net/~dustin/kittycode/
 *
 * Modified for use with PHP by Dustin Grau dgrau@spsu.edu
 *
 * Made functional for inclusion into the mediadb component of
 * phpGroupWare by Sam Wynn neotexan@wynnsite.com
 */

function cue_decode($raw)
{
    $chars =
        "abcdefghijklmnopqrstuvwxyz".
        "ABCDEFGHIJKLMNOPQRSTUVWXYZ".
        "0123456789+-";

    $decoded = "";

    if ($raw != "")
    {
        $component = preg_split("/\./",$raw);

        for ($part=1; $part < 4; $part++)
        {
            $code = $component[$part];
            
            $packer = 0;
            $count  = 0;
            $dc     = "";
            
            for ($char_pos=0; $char_pos < strlen($code); $char_pos++)
            {
		$index = strpos($chars,substr($code,$char_pos,1));
                
		if($index < 0)
                {
                    $dc .= " > " + substr($code,$char_pos,1) + " < ";
                    continue;
		}
                
		$count++;

		$packer = ($packer << 6 | $index);

		if ($count == 4)
                {
                    $dc .= chr(($packer >> 16) ^ 67);
                    $dc .= chr(($packer >> 8 & 255) ^ 67);
                    $dc .= chr(($packer & 255) ^ 67);
                    $count = 0;
                    $packer = 0;
		}
            }
            
            if ($count == 2)
            {
		$dc .= chr((( ($packer << 12) >> 16) ^ 67));
            }
            else if ($count == 3)
            {
		$dc .= chr(( ($packer << 6) >> 16) ^ 67);
		$dc .= chr(( ($packer << 6) >> 8 & 255) ^ 67);
            }
             
            if ($decoded["type"] == "CC!")
            {
                $str[1] = $decoded["type"][2];
                $str[2] = $dc;
                for ($l=1; $l <= 2; $l++)
                {
                    for ($k=0; $k < strlen($str[$l]); $k++)
                    {
                        $index = ord(substr($str[$l],$k,1)) - 32;
                        if ($index < 10)
                        {
                            $index = "0" . $index;
                        }
                        $result[$l] .= $index . " ";
                    }
                }
                $dc = "C " . $result[1] . $result[2];
                $decoded["type"] = '$decoded["type"] (:CueCat)';
            }
            
            switch ($part)
            {
              case 1:
                $decoded["serial"]  = $dc;
                break;
              case 2:
                $decoded["type"]    = $dc;
                break;
              case 3:
                $decoded["barcode"] = $dc;
                break;
            }
        }
    }
    return($decoded);
}

function cue_interpret($decoded)
{
    $upcisbn = array(
        "014794","08041",
        "018926","0445",
        "027778","0449",
        "037145","0812",
        "042799","0785",
        "043144","0688",
        "044903","0312",
        "045863","0517",
        "046594","0064",
        "047132","0152",
        "051487","08167",
        "051488","0140",
        "060771","0002",
        "065373","0373",
        "070992","0523",
        "070993","0446",
        "070999","0345",
        "071001","0380",
        "071009","0440",
        "071125","088677",
        "071136","0451",
        "071149","0451",
        "071152","0515",
        "071162","0451",
        "071268","08217",
        "071831","0425",
        "071842","08439",
        "072742","0441",
        "076714","0671",
        "076783","0770", 
        "076814","0449",
        "078021","0872",
        "079808","0394",
        "090129","0679",
        "099455","0061",
        "099769","0451"
        );

    $interpreted = "";
    
    if (is_array($decoded))
    {
        if (strlen($decoded["barcode"]) == 12)
        {
            for ($o=0; $o < 11; $o+=2)
            {
                $oddSum += substr($decoded["barcode"],$o,1);
            }
            for ($e=1; $e < 11; $e+=2)
            {
                $evenSum += substr($decoded["barcode"],$e,1);
            }
            $n = ($oddSum * 3) + $evenSum;
            $x = $n % 10;
            if ($x == 0)
            {
                $x = 10;
            }
            $checkDigit = 10 - $x;
            if (substr($decoded["barcode"],11,1) != $checkDigit)
            {
                // bad
                $interpreted["checksum"] = 0;
            }
            else
            {
                // good
                $interpreted["checksum"] = 1;
            }
        }
        
        switch ($decoded["type"])
        {
          case "UPA":
          case "UPC":
          case "UPE":
            $interpreted["link"]   = 
                "http://www.upcdatabase.com/item.pl?upc=".$decoded["barcode"];
            break;

          case "IBN":
            $interpreted["isbn"]    = substr($decoded["barcode"],3,9);
            $interpreted["price"]   = "None Encoded";
            $interpreted["country"] = "None Encoded";
            break;		   

          case "IB5":
            $interpreted["isbn"]    = substr($decoded["barcode"],3,9);

            $interpreted["price"]   = substr($decoded["barcode"],14,1);
            if ($interpreted["price"] == "0")
            {
                $interpreted["price"] = "None Encoded";
            }
            else
            {
                $interpreted["price"] = "$"
                    . str_replace("0","",$interpreted["price"]);
            }

            switch (substr($decoded["barcode"],12,1))
            {
              case 5:
                $interpreted["country"]="USA"; 
                break;
              case 6:
                $interpreted["country"]="CANADA";
                break;
              case 9:
                $interpreted["country"]="OTHER";
                break;
              default:
                $interpreted["country"] = "None Encoded";
                break;
            }
            break;		   

          case "C39":
            $interpreted["isbn"] = $decoded["barcode"];
            break;

          case "UA5":
            $result = substr($decoded["barcode"],0,6);
            for ($i=0; $i < count($upcisbn); $i+=2)
            {
                if ($upcisbn[$i] == $result)
                {
                    $result = $upcisbn[$i+1];
                    break;
                }
            }
            $interpreted["isbn"] = $result . substr($decoded["barcode"],12,5);

            $interpreted["price"]   = substr($decoded["barcode"],7,4);
            if ($interpreted["price"] == "0")
            {
                $interpreted["price"] = "None Encoded";
            }
            else
            {
                $interpreted["price"] = "$"
                    . str_replace("0","",$interpreted["price"]);
            }

            switch (substr($decoded["barcode"],5,1))
            {
              case 5:
                $interpreted["country"]="USA"; 
                break;
              case 6:
                $interpreted["country"]="CANADA";
                break;
              case 9:
                $interpreted["country"]="OTHER";
                break;
              default:
                $interpreted["country"] = "None Encoded";
                break;
            }
            break;
        }
        
        if ($interpreted["isbn"] != "")
        {
            $len = strlen($interpreted["isbn"]);
            if (($len > 10) || ($len < 9))
            {
                $error = "INVALID";
            }
            else
            {
                $len = 9;
            }
            for ($i=0; $i < $len; $i++)
            {
                $sum += ($i + 1) * (substr($interpreted["isbn"],$i,1));
            }
            $result = $sum % 11;
            $str = substr($interpreted["isbn"],0,9);
            if ($result == 10)
            {
                $str .= "X";
            }
            else
            {
                $str .= $result;
            }
            if (isset($error))
            {
                $interpreted["isbn"] = $error;
            }
            else
            {
                $interpreted["isbn"] = $str;
            }
            
        }	
    }
    return($interpreted);
}

function cue_links($isbn="INVALID")
{
    $links = "";
    
    if ($isbn != "INVALID")
    {
        $links["isbn"]   = "http://isbn.nu/$isbn/price";
        $links["Amazon"] = "http://www.amazon.com/exec/obidos/ASIN/$isbn";
        $links["BandN"]  = "http://shop.barnesandnoble.com/bookSearch/"
            ."isbnInquiry.asp?isbn=$isbn";
    }
    return($links);
}

?>
