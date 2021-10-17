<?php

namespace App\Service;

class Functions
{

    public function ConvertionArrayStr($value_critere)
    {
        $TypeCritere = gettype($value_critere);
        $Valeurstr   = "";

        if ($TypeCritere == "array") {
            if (count($value_critere) > 0) {
                $Valeurstr = implode(', ', $value_critere);
            } else {
                $Valeurstr = "";
            }
        } else {
            $Valeurstr = $value_critere;
        }
        return $Valeurstr;

    }

    public function extractIds($array, $indicateur, $clear = true)
    {
        $i        = 0;
        $array_id = array();

        foreach ($array as $value) {
            $array_id[$i] = $value[$indicateur];
            $i++;
        }
        if ($clear) {
            $array_id = array_unique($array_id);
        }

        return $array_id;
    }

    public function convert_csv_file($file)
    {
        // recupere le csv provenant de l'API
        $results = array();
        // convertit le csv en tableau
        function convert_array(&$row, $key, $header)
        {
            $row = array_combine($header, $row);
        };
        $array  = array_map('str_getcsv', file($file));
        $header = array_shift($array);
        array_walk($array, 'convert_array', $header);

        // creer un nouveau tableau avec les données qui nous interresse
        foreach ($array as $row) {
            $results[] = [$row['C_LandingPage'], $row['C_Language'], $row['C_OperatingSystem'], $row['S_CompanyName'],
                $row['S_Siren'], $row['S_Siret'], $row['C_ScreenResolution'], date('Y-m-d H:i:s', $row['C_Time']), $row['C_WebBrowser'], $row['S_Country']];
        }

        // met a null en base si la valeur n'est pas renseignée
        for ($i = 0; $i < sizeof($results); $i++) {
            for ($j = 0; $j < sizeof($results[$i]); $j++) {
                if ($results[$i][$j] == '') {
                    $results[$i][$j] = null;
                }
            }
        }
        return $results;
    }

    public function getYears($max)
    {
        $years = array();

        $curr_year = date('Y');

        for ($i = ($curr_year - 3); $i <= ($curr_year + $max); $i++) {
            array_push($years, $i);
        }

        return $years;
    }

    public function array_key_last($ll)
    {
        $key = NULL;

        if (is_array($ll)) {

            end($ll);
            $key = key($ll);
        }

        return $key;
    }

    public function convertDate($date)
    {
        if (preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])(\/|-|.)(0[1-9]|1[0-2])(\/|-|.)[0-9]{4}$/", $date)) {
            return substr($date, 6, 4) . substr($date, 5, 1) . substr($date, 3, 2) . substr($date, 2, 1) . substr($date, 0, 2);
        }
    }

    public function array_sort($array, $on, $order = SORT_ASC)
    { // tri un tableau sur la valeur d'une clé
        $new_array      = array();
        $sortable_array = array();
        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }
            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }
            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }
        return $new_array;
    }

    public function resolve($value, $if, $def, $value_to_print = null)
    {
        if ($value === $if) {
            return $def;
        } else {
            if ($value_to_print == null) {
                return $value;
            } else {
                return $value_to_print;
            }
        }
    }

    public function format_phone($telephone_brut, $fullName)
    {
        if ($telephone_brut == 'NC') {
            return $telephone_brut;
        } else {
            $telephone          = str_replace(' ', '', $telephone_brut);
            $telephone_formated = preg_replace("/([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/", "$1.$2.$3.$4.$5", $telephone);

            return '<a class="phone_mail_web" onclick="auto_commentaire(\'' . $fullName . '\', \'' . $telephone_formated . '\', \'phone\')">' . $telephone_formated . '</a>';
        }
    }

    public function format_mail($mail, $fullName)
    {
        if ($mail == 'NC') {
            return $mail;
        } else {
            return '<a class="phone_mail_web" onclick="auto_commentaire(\'' . $fullName . '\', \'' . $mail . '\', \'mail\')">' . $mail . '</a>';
        }
    }

    public function format_site_web($site_web)
    {
        if ($site_web == 'NC') {
            return $site_web;
        } else {
            return '<a class="phone_mail_web" onclick="window.open(this.href); return false;" href="' . $site_web . '">' . $site_web . '</a>';
        }
    }

    public function format_date_fr_toStr($str_date)
    {
        $date = date_create($str_date);
        return date_format($date, 'd/m/Y');
    }

    public function format_date_us_toStr($str_date)
    {
        $date = date_create($str_date);
        return date_format($date, 'Y/m/d');
    }

    public function showPre($value)
    {
        echo '<pre>';
        if (is_array($value)) {
            print_r($value);
        } else {
            echo $value;
        }
        echo '</pre>';
    }

    public function encode_array($array, $encode_utf8 = true, $addslashes = false)
    {
        foreach ($array as $key => $value) {
            if (empty($value)) {
                $value = 'Rien à signaler';
            } else {
                if ($encode_utf8) {
                    $value = utf8_encode($value);
                }
                if ($addslashes) {
                    $value = addslashes($value);
                }

                //Encode les retour ï¿½ la ligne et ajoute les slashes (pour les quotes)
                $value = preg_replace('#\R+#', '<br>', $value);
                //$value = htmlentities ($value);
            }

            $array[$key] = $value;
        }
        return $array;
    }

}
