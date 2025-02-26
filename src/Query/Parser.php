<?php

namespace Sunhill\Query;

class Parser extends QueryHandler
{
    const GRAMMAR = [
        'EXPRESSION'=>[['EXPRESSION','||','XOREXPRESSION'],['XOREXPRESSION']],
        'XOREXPRESSION'=>[['XOREXPRESSION','xor','ANDEXPRESSION'],['ANDEXPRESSION']],
        'ANDEXPRESSION'=>[['ANDEXPRESSION','and','COMPEXPRESSION'],['COMPEXPRESSION]],
        'COMPEXPRESSION'=>[
            ['COMPEXPRESSION','=','BETWEENEXPRESSION'],
            ['COMPEXPRESSION','!=','BETWEENEXPRESSION'],
            ['COMPEXPRESSION','>=','BETWEENEXPRESSION'],
            ['COMPEXPRESSION','<=','BETWEENEXPRESSION'],
            ['COMPEXPRESSION','>','BETWEENEXPRESSION'],
            ['COMPEXPRESSION','<','BETWEENEXPRESSION'],
            ['COMPEXPRESSION','<=>','BETWEENEXPRESSION'],
            ['COMPEXPRESSION','IS','BETWEENEXPRESSION'],
            ['BETWEENEXPRESSION'],
        ],
        'BETWEENEXPRESSION'=>[['BETWEEN','BETWEENLIMIT','AND','BETWEENLIMIT'],['LIKEEXPRESSION']],
        'VALUEEXPRESSION'=>[['const'],['field']],
        'LIKEEXPRESSION'=>[['VALUEFIELD','like','VALUEFIELD'],['VALUEFIELD','regexp','VALUEFIELD'],['VALUEFIELD','in','VALUEFIELD'],['BITWISEOR']],
        'BITWISEOR'=>[['VALUEFIELD','|','VALUEFIELD'],['BITWISEAND']],
        'BITWISEAND'=>[['VALUEFIELD','&','VALUEFIELD'],['SHIFTEXPRESSION']],
        'SHIFTEXPRESSION'=>[['VALUEFIELD','>>','VALUEFIELD'],['VALUEFIELD','<<','VALUEFIELD'],['ADDEXPRESSION']],
    [
    ['EXPRESSION','+','SUM'],['EXPRESSION','-','SUM'],['SUM'],['€']],
        'SUM'=>['SUM|*|FAKTOR','SUM|/|FAKTOR','SUM|%|FAKTOR','SUM|mod|FAKTOR','SUM|div|FAKTOR','FAKTOR','€'],
        'FAKTOR'=>['(|EXPRESSION|]','const','field','field|as|ident','FUNCT','€'],
        'FUNCT'=>['ident|(|LIST|)'],
        'LIST'=>['EXPRESSION','EXPRESSION|,|EXPRESSION','€'],
        'ORDER'=>['field','field|asc','field|desc'],
        'ASSIGN'=>['field','=','EXPRESSION'], // For set statements
    ];
}  
