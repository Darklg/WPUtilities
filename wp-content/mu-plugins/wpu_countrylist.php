<?php

/*
Plugin Name: WPU Country list
Description: Retrieve a list of countries
Version: 0.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
Thanks: To PLSoucy - http://blog.plsoucy.com/2012/04/iso-3166-country-code-list-csv-sql/
*/

class WPUCountryList {

    private $list = array(
        'AD' => array(
            'en' => 'Andorra',
            'fr' => 'Andorre',
        ) ,
        'AE' => array(
            'en' => 'United Arab Emirates',
            'fr' => 'Émirats arabes unis',
        ) ,
        'AF' => array(
            'en' => 'Afghanistan',
            'fr' => 'Afghanistan',
        ) ,
        'AG' => array(
            'en' => 'Antigua and Barbuda',
            'fr' => 'Antigua-et-Barbuda',
        ) ,
        'AI' => array(
            'en' => 'Anguilla',
            'fr' => 'Anguilla',
        ) ,
        'AL' => array(
            'en' => 'Albania',
            'fr' => 'Albanie',
        ) ,
        'AM' => array(
            'en' => 'Armenia',
            'fr' => 'Arménie',
        ) ,
        'AO' => array(
            'en' => 'Angola',
            'fr' => 'Angola',
        ) ,
        'AQ' => array(
            'en' => 'Antarctica',
            'fr' => 'Antarctique',
        ) ,
        'AR' => array(
            'en' => 'Argentina',
            'fr' => 'Argentine',
        ) ,
        'AS' => array(
            'en' => 'American Samoa',
            'fr' => 'Samoa américaine',
        ) ,
        'AT' => array(
            'en' => 'Austria',
            'fr' => 'Autriche',
        ) ,
        'AU' => array(
            'en' => 'Australia',
            'fr' => 'Australie',
        ) ,
        'AW' => array(
            'en' => 'Aruba',
            'fr' => 'Aruba',
        ) ,
        'AX' => array(
            'en' => 'Åland Islands',
            'fr' => 'Îles d\'Åland',
        ) ,
        'AZ' => array(
            'en' => 'Azerbaijan',
            'fr' => 'Azerbaïdjan',
        ) ,
        'BA' => array(
            'en' => 'Bosnia and Herzegovina',
            'fr' => 'Bosnie-Herzégovine',
        ) ,
        'BB' => array(
            'en' => 'Barbados',
            'fr' => 'Barbade',
        ) ,
        'BD' => array(
            'en' => 'Bangladesh',
            'fr' => 'Bangladesh',
        ) ,
        'BE' => array(
            'en' => 'Belgium',
            'fr' => 'Belgique',
        ) ,
        'BF' => array(
            'en' => 'Burkina Faso',
            'fr' => 'Burkina Faso',
        ) ,
        'BG' => array(
            'en' => 'Bulgaria',
            'fr' => 'Bulgarie',
        ) ,
        'BH' => array(
            'en' => 'Bahrain',
            'fr' => 'Bahreïn',
        ) ,
        'BI' => array(
            'en' => 'Burundi',
            'fr' => 'Burundi',
        ) ,
        'BJ' => array(
            'en' => 'Benin',
            'fr' => 'Bénin',
        ) ,
        'BL' => array(
            'en' => 'Saint Barthélemy',
            'fr' => 'Saint-Barthélemy',
        ) ,
        'BM' => array(
            'en' => 'Bermuda',
            'fr' => 'Bermudes',
        ) ,
        'BN' => array(
            'en' => 'Brunei Darussalam',
            'fr' => 'Brunei Darussalam',
        ) ,
        'BO' => array(
            'en' => 'Bolivia',
            'fr' => 'Bolivie',
        ) ,
        'BQ' => array(
            'en' => 'Caribbean Netherlands ',
            'fr' => 'Pays-Bas caribéens',
        ) ,
        'BR' => array(
            'en' => 'Brazil',
            'fr' => 'Brésil',
        ) ,
        'BS' => array(
            'en' => 'Bahamas',
            'fr' => 'Bahamas',
        ) ,
        'BT' => array(
            'en' => 'Bhutan',
            'fr' => 'Bhoutan',
        ) ,
        'BV' => array(
            'en' => 'Bouvet Island',
            'fr' => 'Île Bouvet',
        ) ,
        'BW' => array(
            'en' => 'Botswana',
            'fr' => 'Botswana',
        ) ,
        'BY' => array(
            'en' => 'Belarus',
            'fr' => 'Bélarus',
        ) ,
        'BZ' => array(
            'en' => 'Belize',
            'fr' => 'Belize',
        ) ,
        'CA' => array(
            'en' => 'Canada',
            'fr' => 'Canada',
        ) ,
        'CC' => array(
            'en' => 'Cocos (Keeling) Islands',
            'fr' => 'Îles Cocos (Keeling)',
        ) ,
        'CD' => array(
            'en' => 'Democratic Republic of Congo',
            'fr' => 'République démocratique du Congo',
        ) ,
        'CF' => array(
            'en' => 'Central African Republic',
            'fr' => 'République centrafricaine',
        ) ,
        'CG' => array(
            'en' => 'Congo',
            'fr' => 'Congo',
        ) ,
        'CH' => array(
            'en' => 'Switzerland',
            'fr' => 'Suisse',
        ) ,
        'CI' => array(
            'en' => 'Côte d\'Ivoire',
            'fr' => 'Côte d\'Ivoire',
        ) ,
        'CK' => array(
            'en' => 'Cook Islands',
            'fr' => 'Îles Cook',
        ) ,
        'CL' => array(
            'en' => 'Chile',
            'fr' => 'Chili',
        ) ,
        'CM' => array(
            'en' => 'Cameroon',
            'fr' => 'Cameroun',
        ) ,
        'CN' => array(
            'en' => 'China',
            'fr' => 'Chine',
        ) ,
        'CO' => array(
            'en' => 'Colombia',
            'fr' => 'Colombie',
        ) ,
        'CR' => array(
            'en' => 'Costa Rica',
            'fr' => 'Costa Rica',
        ) ,
        'CU' => array(
            'en' => 'Cuba',
            'fr' => 'Cuba',
        ) ,
        'CV' => array(
            'en' => 'Cape Verde',
            'fr' => 'Cap-Vert',
        ) ,
        'CW' => array(
            'en' => 'Curaçao',
            'fr' => 'Curaçao',
        ) ,
        'CX' => array(
            'en' => 'Christmas Island',
            'fr' => 'Île Christmas',
        ) ,
        'CY' => array(
            'en' => 'Cyprus',
            'fr' => 'Chypre',
        ) ,
        'CZ' => array(
            'en' => 'Czech Republic',
            'fr' => 'République tchèque',
        ) ,
        'DE' => array(
            'en' => 'Germany',
            'fr' => 'Allemagne',
        ) ,
        'DJ' => array(
            'en' => 'Djibouti',
            'fr' => 'Djibouti',
        ) ,
        'DK' => array(
            'en' => 'Denmark',
            'fr' => 'Danemark',
        ) ,
        'DM' => array(
            'en' => 'Dominica',
            'fr' => 'Dominique',
        ) ,
        'DO' => array(
            'en' => 'Dominican Republic',
            'fr' => 'République dominicaine',
        ) ,
        'DZ' => array(
            'en' => 'Algeria',
            'fr' => 'Algérie',
        ) ,
        'EC' => array(
            'en' => 'Ecuador',
            'fr' => 'Équateur',
        ) ,
        'EE' => array(
            'en' => 'Estonia',
            'fr' => 'Estonie',
        ) ,
        'EG' => array(
            'en' => 'Egypt',
            'fr' => 'Égypte',
        ) ,
        'EH' => array(
            'en' => 'Western Sahara',
            'fr' => 'Sahara Occidental',
        ) ,
        'ER' => array(
            'en' => 'Eritrea',
            'fr' => 'Érythrée',
        ) ,
        'ES' => array(
            'en' => 'Spain',
            'fr' => 'Espagne',
        ) ,
        'ET' => array(
            'en' => 'Ethiopia',
            'fr' => 'Éthiopie',
        ) ,
        'FI' => array(
            'en' => 'Finland',
            'fr' => 'Finlande',
        ) ,
        'FJ' => array(
            'en' => 'Fiji',
            'fr' => 'Fidji',
        ) ,
        'FK' => array(
            'en' => 'Falkland Islands',
            'fr' => 'Îles Malouines',
        ) ,
        'FM' => array(
            'en' => 'Federated States of Micronesia',
            'fr' => 'États fédérés de Micronésie',
        ) ,
        'FO' => array(
            'en' => 'Faroe Islands',
            'fr' => 'Îles Féroé',
        ) ,
        'FR' => array(
            'en' => 'France',
            'fr' => 'France',
        ) ,
        'GA' => array(
            'en' => 'Gabon',
            'fr' => 'Gabon',
        ) ,
        'GB' => array(
            'en' => 'United Kingdom',
            'fr' => 'Royaume-Uni',
        ) ,
        'GD' => array(
            'en' => 'Grenada',
            'fr' => 'Grenade',
        ) ,
        'GE' => array(
            'en' => 'Georgia',
            'fr' => 'Géorgie',
        ) ,
        'GF' => array(
            'en' => 'French Guiana',
            'fr' => 'Guyane française',
        ) ,
        'GG' => array(
            'en' => 'Guernsey',
            'fr' => 'Guernesey',
        ) ,
        'GH' => array(
            'en' => 'Ghana',
            'fr' => 'Ghana',
        ) ,
        'GI' => array(
            'en' => 'Gibraltar',
            'fr' => 'Gibraltar',
        ) ,
        'GL' => array(
            'en' => 'Greenland',
            'fr' => 'Groenland',
        ) ,
        'GM' => array(
            'en' => 'Gambia',
            'fr' => 'Gambie',
        ) ,
        'GN' => array(
            'en' => 'Guinea',
            'fr' => 'Guinée',
        ) ,
        'GP' => array(
            'en' => 'Guadeloupe',
            'fr' => 'Guadeloupe',
        ) ,
        'GQ' => array(
            'en' => 'Equatorial Guinea',
            'fr' => 'Guinée équatoriale',
        ) ,
        'GR' => array(
            'en' => 'Greece',
            'fr' => 'Grèce',
        ) ,
        'GS' => array(
            'en' => 'South Georgia and the South Sandwich Islands',
            'fr' => 'Géorgie du Sud et les îles Sandwich du Sud',
        ) ,
        'GT' => array(
            'en' => 'Guatemala',
            'fr' => 'Guatemala',
        ) ,
        'GU' => array(
            'en' => 'Guam',
            'fr' => 'Guam',
        ) ,
        'GW' => array(
            'en' => 'Guinea-Bissau',
            'fr' => 'Guinée-Bissau',
        ) ,
        'GY' => array(
            'en' => 'Guyana',
            'fr' => 'Guyana',
        ) ,
        'HK' => array(
            'en' => 'Hong Kong',
            'fr' => 'Hong Kong',
        ) ,
        'HM' => array(
            'en' => 'Heard and McDonald Islands',
            'fr' => 'Îles Heard et McDonald',
        ) ,
        'HN' => array(
            'en' => 'Honduras',
            'fr' => 'Honduras',
        ) ,
        'HR' => array(
            'en' => 'Croatia',
            'fr' => 'Croatie',
        ) ,
        'HT' => array(
            'en' => 'Haiti',
            'fr' => 'Haïti',
        ) ,
        'HU' => array(
            'en' => 'Hungary',
            'fr' => 'Hongrie',
        ) ,
        'ID' => array(
            'en' => 'Indonesia',
            'fr' => 'Indonésie',
        ) ,
        'IE' => array(
            'en' => 'Ireland',
            'fr' => 'Irlande',
        ) ,
        'IL' => array(
            'en' => 'Israel',
            'fr' => 'Israël',
        ) ,
        'IM' => array(
            'en' => 'Isle of Man',
            'fr' => 'Île de Man',
        ) ,
        'IN' => array(
            'en' => 'India',
            'fr' => 'Inde',
        ) ,
        'IO' => array(
            'en' => 'British Indian Ocean Territory',
            'fr' => 'Territoire britannique de l\'océan Indien',
        ) ,
        'IQ' => array(
            'en' => 'Iraq',
            'fr' => 'Irak',
        ) ,
        'IR' => array(
            'en' => 'Iran',
            'fr' => 'Iran',
        ) ,
        'IS' => array(
            'en' => 'Iceland',
            'fr' => 'Islande',
        ) ,
        'IT' => array(
            'en' => 'Italy',
            'fr' => 'Italie',
        ) ,
        'JE' => array(
            'en' => 'Jersey',
            'fr' => 'Jersey',
        ) ,
        'JM' => array(
            'en' => 'Jamaica',
            'fr' => 'Jamaïque',
        ) ,
        'JO' => array(
            'en' => 'Jordan',
            'fr' => 'Jordanie',
        ) ,
        'JP' => array(
            'en' => 'Japan',
            'fr' => 'Japon',
        ) ,
        'KE' => array(
            'en' => 'Kenya',
            'fr' => 'Kenya',
        ) ,
        'KG' => array(
            'en' => 'Kyrgyzstan',
            'fr' => 'Kirghizistan',
        ) ,
        'KH' => array(
            'en' => 'Cambodia',
            'fr' => 'Cambodge',
        ) ,
        'KI' => array(
            'en' => 'Kiribati',
            'fr' => 'Kiribati',
        ) ,
        'KM' => array(
            'en' => 'Comoros',
            'fr' => 'Comores',
        ) ,
        'KN' => array(
            'en' => 'Saint Kitts and Nevis',
            'fr' => 'Saint-Kitts-et-Nevis',
        ) ,
        'KP' => array(
            'en' => 'North Korea',
            'fr' => 'Corée du Nord',
        ) ,
        'KR' => array(
            'en' => 'South Korea',
            'fr' => 'Corée du Sud',
        ) ,
        'KW' => array(
            'en' => 'Kuwait',
            'fr' => 'Koweït',
        ) ,
        'KY' => array(
            'en' => 'Cayman Islands',
            'fr' => 'Îles Caïmans',
        ) ,
        'KZ' => array(
            'en' => 'Kazakhstan',
            'fr' => 'Kazakhstan',
        ) ,
        'LA' => array(
            'en' => 'Lao People\'s Democratic Republic',
            'fr' => 'Laos',
        ) ,
        'LB' => array(
            'en' => 'Lebanon',
            'fr' => 'Liban',
        ) ,
        'LC' => array(
            'en' => 'Saint Lucia',
            'fr' => 'Sainte-Lucie',
        ) ,
        'LI' => array(
            'en' => 'Liechtenstein',
            'fr' => 'Liechtenstein',
        ) ,
        'LK' => array(
            'en' => 'Sri Lanka',
            'fr' => 'Sri Lanka',
        ) ,
        'LR' => array(
            'en' => 'Liberia',
            'fr' => 'Libéria',
        ) ,
        'LS' => array(
            'en' => 'Lesotho',
            'fr' => 'Lesotho',
        ) ,
        'LT' => array(
            'en' => 'Lithuania',
            'fr' => 'Lituanie',
        ) ,
        'LU' => array(
            'en' => 'Luxembourg',
            'fr' => 'Luxembourg',
        ) ,
        'LV' => array(
            'en' => 'Latvia',
            'fr' => 'Lettonie',
        ) ,
        'LY' => array(
            'en' => 'Libya',
            'fr' => 'Libye',
        ) ,
        'MA' => array(
            'en' => 'Morocco',
            'fr' => 'Maroc',
        ) ,
        'MC' => array(
            'en' => 'Monaco',
            'fr' => 'Monaco',
        ) ,
        'MD' => array(
            'en' => 'Moldova',
            'fr' => 'Moldavie',
        ) ,
        'ME' => array(
            'en' => 'Montenegro',
            'fr' => 'Monténégro',
        ) ,
        'MF' => array(
            'en' => 'Saint-Martin (France)',
            'fr' => 'Saint-Martin (France)',
        ) ,
        'MG' => array(
            'en' => 'Madagascar',
            'fr' => 'Madagascar',
        ) ,
        'MH' => array(
            'en' => 'Marshall Islands',
            'fr' => 'Îles Marshall',
        ) ,
        'MK' => array(
            'en' => 'Macedonia',
            'fr' => 'Macédoine',
        ) ,
        'ML' => array(
            'en' => 'Mali',
            'fr' => 'Mali',
        ) ,
        'MM' => array(
            'en' => 'Myanmar',
            'fr' => 'Myanmar',
        ) ,
        'MN' => array(
            'en' => 'Mongolia',
            'fr' => 'Mongolie',
        ) ,
        'MO' => array(
            'en' => 'Macau',
            'fr' => 'Macao',
        ) ,
        'MP' => array(
            'en' => 'Northern Mariana Islands',
            'fr' => 'Mariannes du Nord',
        ) ,
        'MQ' => array(
            'en' => 'Martinique',
            'fr' => 'Martinique',
        ) ,
        'MR' => array(
            'en' => 'Mauritania',
            'fr' => 'Mauritanie',
        ) ,
        'MS' => array(
            'en' => 'Montserrat',
            'fr' => 'Montserrat',
        ) ,
        'MT' => array(
            'en' => 'Malta',
            'fr' => 'Malte',
        ) ,
        'MU' => array(
            'en' => 'Mauritius',
            'fr' => 'Maurice',
        ) ,
        'MV' => array(
            'en' => 'Maldives',
            'fr' => 'Maldives',
        ) ,
        'MW' => array(
            'en' => 'Malawi',
            'fr' => 'Malawi',
        ) ,
        'MX' => array(
            'en' => 'Mexico',
            'fr' => 'Mexique',
        ) ,
        'MY' => array(
            'en' => 'Malaysia',
            'fr' => 'Malaisie',
        ) ,
        'MZ' => array(
            'en' => 'Mozambique',
            'fr' => 'Mozambique',
        ) ,
        'NA' => array(
            'en' => 'Namibia',
            'fr' => 'Namibie',
        ) ,
        'NC' => array(
            'en' => 'New Caledonia',
            'fr' => 'Nouvelle-Calédonie',
        ) ,
        'NE' => array(
            'en' => 'Niger',
            'fr' => 'Niger',
        ) ,
        'NF' => array(
            'en' => 'Norfolk Island',
            'fr' => 'Île Norfolk',
        ) ,
        'NG' => array(
            'en' => 'Nigeria',
            'fr' => 'Nigeria',
        ) ,
        'NI' => array(
            'en' => 'Nicaragua',
            'fr' => 'Nicaragua',
        ) ,
        'NL' => array(
            'en' => 'The Netherlands',
            'fr' => 'Pays-Bas',
        ) ,
        'NO' => array(
            'en' => 'Norway',
            'fr' => 'Norvège',
        ) ,
        'NP' => array(
            'en' => 'Nepal',
            'fr' => 'Népal',
        ) ,
        'NR' => array(
            'en' => 'Nauru',
            'fr' => 'Nauru',
        ) ,
        'NU' => array(
            'en' => 'Niue',
            'fr' => 'Niue',
        ) ,
        'NZ' => array(
            'en' => 'New Zealand',
            'fr' => 'Nouvelle-Zélande',
        ) ,
        'OM' => array(
            'en' => 'Oman',
            'fr' => 'Oman',
        ) ,
        'PA' => array(
            'en' => 'Panama',
            'fr' => 'Panama',
        ) ,
        'PE' => array(
            'en' => 'Peru',
            'fr' => 'Pérou',
        ) ,
        'PF' => array(
            'en' => 'French Polynesia',
            'fr' => 'Polynésie française',
        ) ,
        'PG' => array(
            'en' => 'Papua New Guinea',
            'fr' => 'Papouasie-Nouvelle-Guinée',
        ) ,
        'PH' => array(
            'en' => 'Philippines',
            'fr' => 'Philippines',
        ) ,
        'PK' => array(
            'en' => 'Pakistan',
            'fr' => 'Pakistan',
        ) ,
        'PL' => array(
            'en' => 'Poland',
            'fr' => 'Pologne',
        ) ,
        'PM' => array(
            'en' => 'St. Pierre and Miquelon',
            'fr' => 'Saint-Pierre-et-Miquelon',
        ) ,
        'PN' => array(
            'en' => 'Pitcairn',
            'fr' => 'Pitcairn',
        ) ,
        'PR' => array(
            'en' => 'Puerto Rico',
            'fr' => 'Puerto Rico',
        ) ,
        'PS' => array(
            'en' => 'State of Palestine',
            'fr' => 'Palestine',
        ) ,
        'PT' => array(
            'en' => 'Portugal',
            'fr' => 'Portugal',
        ) ,
        'PW' => array(
            'en' => 'Palau',
            'fr' => 'Palau',
        ) ,
        'PY' => array(
            'en' => 'Paraguay',
            'fr' => 'Paraguay',
        ) ,
        'QA' => array(
            'en' => 'Qatar',
            'fr' => 'Qatar',
        ) ,
        'RE' => array(
            'en' => 'Réunion',
            'fr' => 'Réunion',
        ) ,
        'RO' => array(
            'en' => 'Romania',
            'fr' => 'Roumanie',
        ) ,
        'RS' => array(
            'en' => 'Serbia',
            'fr' => 'Serbie',
        ) ,
        'RU' => array(
            'en' => 'Russian Federation',
            'fr' => 'Russie',
        ) ,
        'RW' => array(
            'en' => 'Rwanda',
            'fr' => 'Rwanda',
        ) ,
        'SA' => array(
            'en' => 'Saudi Arabia',
            'fr' => 'Arabie saoudite',
        ) ,
        'SB' => array(
            'en' => 'Solomon Islands',
            'fr' => 'Îles Salomon',
        ) ,
        'SC' => array(
            'en' => 'Seychelles',
            'fr' => 'Seychelles',
        ) ,
        'SD' => array(
            'en' => 'Sudan',
            'fr' => 'Soudan',
        ) ,
        'SE' => array(
            'en' => 'Sweden',
            'fr' => 'Suède',
        ) ,
        'SG' => array(
            'en' => 'Singapore',
            'fr' => 'Singapour',
        ) ,
        'SH' => array(
            'en' => 'Saint Helena',
            'fr' => 'Sainte-Hélène',
        ) ,
        'SI' => array(
            'en' => 'Slovenia',
            'fr' => 'Slovénie',
        ) ,
        'SJ' => array(
            'en' => 'Svalbard and Jan Mayen Islands',
            'fr' => 'Svalbard et île de Jan Mayen',
        ) ,
        'SK' => array(
            'en' => 'Slovakia',
            'fr' => 'Slovaquie',
        ) ,
        'SL' => array(
            'en' => 'Sierra Leone',
            'fr' => 'Sierra Leone',
        ) ,
        'SM' => array(
            'en' => 'San Marino',
            'fr' => 'Saint-Marin',
        ) ,
        'SN' => array(
            'en' => 'Senegal',
            'fr' => 'Sénégal',
        ) ,
        'SO' => array(
            'en' => 'Somalia',
            'fr' => 'Somalie',
        ) ,
        'SR' => array(
            'en' => 'Suriname',
            'fr' => 'Suriname',
        ) ,
        'SS' => array(
            'en' => 'South Sudan',
            'fr' => 'Soudan du Sud',
        ) ,
        'ST' => array(
            'en' => 'Sao Tome and Principe',
            'fr' => 'Sao Tomé-et-Principe',
        ) ,
        'SV' => array(
            'en' => 'El Salvador',
            'fr' => 'El Salvador',
        ) ,
        'SX' => array(
            'en' => 'Sint Maarten (Dutch part)',
            'fr' => 'Saint-Martin (Pays-Bas)',
        ) ,
        'SY' => array(
            'en' => 'Syria',
            'fr' => 'Syrie',
        ) ,
        'SZ' => array(
            'en' => 'Swaziland',
            'fr' => 'Swaziland',
        ) ,
        'TC' => array(
            'en' => 'Turks and Caicos Islands',
            'fr' => 'Îles Turks et Caicos',
        ) ,
        'TD' => array(
            'en' => 'Chad',
            'fr' => 'Tchad',
        ) ,
        'TF' => array(
            'en' => 'French Southern Territories',
            'fr' => 'Terres australes françaises',
        ) ,
        'TG' => array(
            'en' => 'Togo',
            'fr' => 'Togo',
        ) ,
        'TH' => array(
            'en' => 'Thailand',
            'fr' => 'Thaïlande',
        ) ,
        'TJ' => array(
            'en' => 'Tajikistan',
            'fr' => 'Tadjikistan',
        ) ,
        'TK' => array(
            'en' => 'Tokelau',
            'fr' => 'Tokelau',
        ) ,
        'TL' => array(
            'en' => 'Timor-Leste',
            'fr' => 'Timor-Leste',
        ) ,
        'TM' => array(
            'en' => 'Turkmenistan',
            'fr' => 'Turkménistan',
        ) ,
        'TN' => array(
            'en' => 'Tunisia',
            'fr' => 'Tunisie',
        ) ,
        'TO' => array(
            'en' => 'Tonga',
            'fr' => 'Tonga',
        ) ,
        'TR' => array(
            'en' => 'Turkey',
            'fr' => 'Turquie',
        ) ,
        'TT' => array(
            'en' => 'Trinidad and Tobago',
            'fr' => 'Trinité-et-Tobago',
        ) ,
        'TV' => array(
            'en' => 'Tuvalu',
            'fr' => 'Tuvalu',
        ) ,
        'TW' => array(
            'en' => 'Taiwan',
            'fr' => 'Taïwan',
        ) ,
        'TZ' => array(
            'en' => 'Tanzania',
            'fr' => 'Tanzanie',
        ) ,
        'UA' => array(
            'en' => 'Ukraine',
            'fr' => 'Ukraine',
        ) ,
        'UG' => array(
            'en' => 'Uganda',
            'fr' => 'Ouganda',
        ) ,
        'UM' => array(
            'en' => 'United States Minor Outlying Islands',
            'fr' => 'Îles mineures éloignées des États-Unis',
        ) ,
        'US' => array(
            'en' => 'United States',
            'fr' => 'États-Unis',
        ) ,
        'UY' => array(
            'en' => 'Uruguay',
            'fr' => 'Uruguay',
        ) ,
        'UZ' => array(
            'en' => 'Uzbekistan',
            'fr' => 'Ouzbékistan',
        ) ,
        'VA' => array(
            'en' => 'Vatican',
            'fr' => 'Vatican',
        ) ,
        'VC' => array(
            'en' => 'Saint Vincent and the Grenadines',
            'fr' => 'Saint-Vincent-et-les-Grenadines',
        ) ,
        'VE' => array(
            'en' => 'Venezuela',
            'fr' => 'Venezuela',
        ) ,
        'VG' => array(
            'en' => 'Virgin Islands (British)',
            'fr' => 'Îles Vierges britanniques',
        ) ,
        'VI' => array(
            'en' => 'Virgin Islands (U.S.)',
            'fr' => 'Îles Vierges américaines',
        ) ,
        'VN' => array(
            'en' => 'Vietnam',
            'fr' => 'Vietnam',
        ) ,
        'VU' => array(
            'en' => 'Vanuatu',
            'fr' => 'Vanuatu',
        ) ,
        'WF' => array(
            'en' => 'Wallis and Futuna Islands',
            'fr' => 'Îles Wallis-et-Futuna',
        ) ,
        'WS' => array(
            'en' => 'Samoa',
            'fr' => 'Samoa',
        ) ,
        'YE' => array(
            'en' => 'Yemen',
            'fr' => 'Yémen',
        ) ,
        'YT' => array(
            'en' => 'Mayotte',
            'fr' => 'Mayotte',
        ) ,
        'ZA' => array(
            'en' => 'South Africa',
            'fr' => 'Afrique du Sud',
        ) ,
        'ZM' => array(
            'en' => 'Zambia',
            'fr' => 'Zambie',
        ) ,
        'ZW' => array(
            'en' => 'Zimbabwe',
            'fr' => 'Zimbabwe',
        ) ,
    );

    function getList() {
        $list = apply_filters('wpucountrylist_list', $this->list);
        $country_code = 'en';
        $current_country = explode('_', get_locale());
        if (array_key_exists($current_country[0], $this->list['FR'])) {
            $country_code = $current_country[0];
        }
        $return_list = array();
        foreach ($list as $code => $country) {
            $return_list[$code] = $country[$country_code];
        }
        return $return_list;
    }
}

$WPUCountryList = new WPUCountryList();

