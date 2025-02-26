<?php

namespace Sunhill\Query;

class Parser extends QueryHandler
{
    const GRAMMAR = [
        'EXPRESSION'=>[['EXPRESSION','||','XOREXPRESSION'],['XOREXPRESSION'],['€']],
        'XOREXPRESSION'=>[['XOREXPRESSION','xor','ANDEXPRESSION'],['ANDEXPRESSION'],['€']],
    [['EXPRESSION','+','SUM'],['EXPRESSION','-','SUM'],['SUM'],['€']],
        'SUM'=>['SUM|*|FAKTOR','SUM|/|FAKTOR','SUM|%|FAKTOR','SUM|mod|FAKTOR','SUM|div|FAKTOR','FAKTOR','€'],
        'FAKTOR'=>['(|EXPRESSION|]','const','field','field|as|ident','FUNCT','€'],
        'FUNCT'=>['ident|(|LIST|)'],
        'LIST'=>['EXPRESSION','EXPRESSION|,|EXPRESSION','€'],
        'ORDER'=>['field','field|asc','field|desc'],
        'ASSIGN'=>['field','=','EXPRESSION'], // For set statements
    ];
}  
