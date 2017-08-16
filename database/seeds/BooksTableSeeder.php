<?php

use Illuminate\Database\Seeder;

class BooksTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('books')->delete();
        
        \DB::table('books')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Gênesis',
                'abbrev' => 'gn',
                'testament' => '1',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Êxodo',
                'abbrev' => 'ex',
                'testament' => '1',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Levítico',
                'abbrev' => 'lv',
                'testament' => '1',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Números',
                'abbrev' => 'nm',
                'testament' => '1',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Deuteronômio',
                'abbrev' => 'dt',
                'testament' => '1',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Josué',
                'abbrev' => 'js',
                'testament' => '1',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Juízes',
                'abbrev' => 'jz',
                'testament' => '1',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Rute',
                'abbrev' => 'rt',
                'testament' => '1',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => '1º Samuel',
                'abbrev' => '1sm',
                'testament' => '1',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => '2º Samuel',
                'abbrev' => '2sm',
                'testament' => '1',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => '1º Reis',
                'abbrev' => '1rs',
                'testament' => '1',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => '2º Reis',
                'abbrev' => '2rs',
                'testament' => '1',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => '1º Crônicas',
                'abbrev' => '1cr',
                'testament' => '1',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => '2º Crônicas',
                'abbrev' => '2cr',
                'testament' => '1',
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'Esdras',
                'abbrev' => 'ed',
                'testament' => '1',
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'Neemias',
                'abbrev' => 'ne',
                'testament' => '1',
            ),
            16 => 
            array (
                'id' => 17,
                'name' => 'Ester',
                'abbrev' => 'et',
                'testament' => '1',
            ),
            17 => 
            array (
                'id' => 18,
                'name' => 'Jó',
                'abbrev' => 'job',
                'testament' => '1',
            ),
            18 => 
            array (
                'id' => 19,
                'name' => 'Salmos',
                'abbrev' => 'sl',
                'testament' => '1',
            ),
            19 => 
            array (
                'id' => 20,
                'name' => 'Provérbios',
                'abbrev' => 'pv',
                'testament' => '1',
            ),
            20 => 
            array (
                'id' => 21,
                'name' => 'Eclesiastes',
                'abbrev' => 'ec',
                'testament' => '1',
            ),
            21 => 
            array (
                'id' => 22,
                'name' => 'Cânticos',
                'abbrev' => 'ct',
                'testament' => '1',
            ),
            22 => 
            array (
                'id' => 23,
                'name' => 'Isaías',
                'abbrev' => 'is',
                'testament' => '1',
            ),
            23 => 
            array (
                'id' => 24,
                'name' => 'Jeremias',
                'abbrev' => 'jr',
                'testament' => '1',
            ),
            24 => 
            array (
                'id' => 25,
                'name' => 'Lamentações de Jeremias',
                'abbrev' => 'lm',
                'testament' => '1',
            ),
            25 => 
            array (
                'id' => 26,
                'name' => 'Ezequiel',
                'abbrev' => 'ez',
                'testament' => '1',
            ),
            26 => 
            array (
                'id' => 27,
                'name' => 'Daniel',
                'abbrev' => 'dn',
                'testament' => '1',
            ),
            27 => 
            array (
                'id' => 28,
                'name' => 'Oséias',
                'abbrev' => 'os',
                'testament' => '1',
            ),
            28 => 
            array (
                'id' => 29,
                'name' => 'Joel',
                'abbrev' => 'jl',
                'testament' => '1',
            ),
            29 => 
            array (
                'id' => 30,
                'name' => 'Amós',
                'abbrev' => 'am',
                'testament' => '1',
            ),
            30 => 
            array (
                'id' => 31,
                'name' => 'Obadias',
                'abbrev' => 'ob',
                'testament' => '1',
            ),
            31 => 
            array (
                'id' => 32,
                'name' => 'Jonas',
                'abbrev' => 'jn',
                'testament' => '1',
            ),
            32 => 
            array (
                'id' => 33,
                'name' => 'Miquéias',
                'abbrev' => 'mq',
                'testament' => '1',
            ),
            33 => 
            array (
                'id' => 34,
                'name' => 'Naum',
                'abbrev' => 'na',
                'testament' => '1',
            ),
            34 => 
            array (
                'id' => 35,
                'name' => 'Habacuque',
                'abbrev' => 'hc',
                'testament' => '1',
            ),
            35 => 
            array (
                'id' => 36,
                'name' => 'Sofonias',
                'abbrev' => 'sf',
                'testament' => '1',
            ),
            36 => 
            array (
                'id' => 37,
                'name' => 'Ageu',
                'abbrev' => 'ag',
                'testament' => '1',
            ),
            37 => 
            array (
                'id' => 38,
                'name' => 'Zacarias',
                'abbrev' => 'zc',
                'testament' => '1',
            ),
            38 => 
            array (
                'id' => 39,
                'name' => 'Malaquias',
                'abbrev' => 'ml',
                'testament' => '1',
            ),
            39 => 
            array (
                'id' => 40,
                'name' => 'Mateus',
                'abbrev' => 'mt',
                'testament' => '1',
            ),
            40 => 
            array (
                'id' => 41,
                'name' => 'Marcos',
                'abbrev' => 'mc',
                'testament' => '1',
            ),
            41 => 
            array (
                'id' => 42,
                'name' => 'Lucas',
                'abbrev' => 'lc',
                'testament' => '1',
            ),
            42 => 
            array (
                'id' => 43,
                'name' => 'João',
                'abbrev' => 'jo',
                'testament' => '1',
            ),
            43 => 
            array (
                'id' => 44,
                'name' => 'Atos',
                'abbrev' => 'at',
                'testament' => '1',
            ),
            44 => 
            array (
                'id' => 45,
                'name' => 'Romanos',
                'abbrev' => 'rm',
                'testament' => '1',
            ),
            45 => 
            array (
                'id' => 46,
                'name' => '1ª Coríntios',
                'abbrev' => '1co',
                'testament' => '1',
            ),
            46 => 
            array (
                'id' => 47,
                'name' => '2ª Coríntios',
                'abbrev' => '2co',
                'testament' => '1',
            ),
            47 => 
            array (
                'id' => 48,
                'name' => 'Gálatas',
                'abbrev' => 'gl',
                'testament' => '1',
            ),
            48 => 
            array (
                'id' => 49,
                'name' => 'Efésios',
                'abbrev' => 'ef',
                'testament' => '1',
            ),
            49 => 
            array (
                'id' => 50,
                'name' => 'Filipenses',
                'abbrev' => 'fp',
                'testament' => '1',
            ),
            50 => 
            array (
                'id' => 51,
                'name' => 'Colossenses',
                'abbrev' => 'cl',
                'testament' => '1',
            ),
            51 => 
            array (
                'id' => 52,
                'name' => '1ª Tessalonicenses',
                'abbrev' => '1ts',
                'testament' => '1',
            ),
            52 => 
            array (
                'id' => 53,
                'name' => '2ª Tessalonicenses',
                'abbrev' => '2ts',
                'testament' => '1',
            ),
            53 => 
            array (
                'id' => 54,
                'name' => '1ª Timóteo',
                'abbrev' => '1tm',
                'testament' => '1',
            ),
            54 => 
            array (
                'id' => 55,
                'name' => '2ª Timóteo',
                'abbrev' => '2tm',
                'testament' => '1',
            ),
            55 => 
            array (
                'id' => 56,
                'name' => 'Tito',
                'abbrev' => 'tt',
                'testament' => '1',
            ),
            56 => 
            array (
                'id' => 57,
                'name' => 'Filemom',
                'abbrev' => 'fm',
                'testament' => '1',
            ),
            57 => 
            array (
                'id' => 58,
                'name' => 'Hebreus',
                'abbrev' => 'hb',
                'testament' => '1',
            ),
            58 => 
            array (
                'id' => 59,
                'name' => 'Tiago',
                'abbrev' => 'tg',
                'testament' => '1',
            ),
            59 => 
            array (
                'id' => 60,
                'name' => '1ª Pedro',
                'abbrev' => '1pe',
                'testament' => '1',
            ),
            60 => 
            array (
                'id' => 61,
                'name' => '2ª Pedro',
                'abbrev' => '2pe',
                'testament' => '1',
            ),
            61 => 
            array (
                'id' => 62,
                'name' => '1ª João',
                'abbrev' => '1jo',
                'testament' => '1',
            ),
            62 => 
            array (
                'id' => 63,
                'name' => '2ª João',
                'abbrev' => '2jo',
                'testament' => '1',
            ),
            63 => 
            array (
                'id' => 64,
                'name' => '3ª João',
                'abbrev' => '3jo',
                'testament' => '1',
            ),
            64 => 
            array (
                'id' => 65,
                'name' => 'Judas',
                'abbrev' => 'jd',
                'testament' => '1',
            ),
            65 => 
            array (
                'id' => 66,
                'name' => 'Apocalipse',
                'abbrev' => 'ap',
                'testament' => '1',
            ),
        ));
        
        
    }
}