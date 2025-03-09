<?php

namespace Sunhill\Query;

use Sunhill\Query\Exceptions\InvalidStatementException;

class QueryParser extends QueryHandler
{
    protected $grammar = [
        'FINAL'=>[
            ['LIST'],
            ['ORDER'],
            ['ASSIGN'],
            ['EXPRESSION']            
        ],
        'LIST'=>[
            ['EXPRESSION',',','EXPRESSION','!execute!'=>'addToList($0,$2)'],
            ['EXPRESSION'],
            ['€']            
        ],
        'ORDER'=>[
            ['field','!execute!'=>'orderStatement($0,"asc")'],
            ['field','asc','!execute!'=>'orderStatement($0,"asc")'],
            ['field','desc','!execute!'=>'orderStatement($0,"asc")']            
        ],
        'ASSIGN'=>[
            ['field','=','EXPRESSION','!execute!'=>'assignStatement($0,$2)'],            
            ['field',':=','EXPRESSION','!execute!'=>'assignStatement($0,$2)'],
        ],
        'EXPRESSION'=>[
            ['EXPRESSION','||','XOREXPRESSION','!execute!'=>'operator($0,$1,$2)'],
            ['XOREXPRESSION']            
        ],
        'XOREXPRESSION'=>[
            ['XOREXPRESSION','xor','ANDEXPRESSION'],
            ['ANDEXPRESSION']            
        ],
        'ANDEXPRESSION'=>[['ANDEXPRESSION','and','COMPEXPRESSION'],['COMPEXPRESSION']],
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
        'LIKEEXPRESSION'=>[['VALUEFIELD','like','VALUEFIELD'],['VALUEFIELD','regexp','VALUEFIELD'],['VALUEFIELD','in','VALUEFIELD'],['BITWISEOR']],
        'BITWISEOR'=>[['VALUEFIELD','|','VALUEFIELD'],['BITWISEAND']],
        'BITWISEAND'=>[['VALUEFIELD','&','VALUEFIELD'],['SHIFTEXPRESSION']],
        'SHIFTEXPRESSION'=>[['VALUEFIELD','>>','VALUEFIELD'],['VALUEFIELD','<<','VALUEFIELD'],['ADDEXPRESSION']],
        'ADDEXPRESSION'=>[['ADDEXPRESSION','+','MULTEXPRESSION'],['ADDEXPRESSION','-','MULTEXPRESSION'],['MULTEXPRESSION']],
        'MULTEXPRESSION'=>[
            ['MULTEXPRESSION','*','BITWISEXOR'],
            ['MULTEXPRESSION','/','BITWISEXOR'],
            ['MULTEXPRESSION','%','BITWISEXOR'],
            ['MULTEXPRESSION','div','BITWISEXOR'],
            ['BITWISEXOR']
         ],
         'BITWISEXOR'=>[['VALUEFIELD','^','VALUEFIELD'],['CONCATATION']],
         'CONCATATION'=>[['CONCATATION','||','UNARYMINUS'],['UNARYMINUS']],
         'UNARYMINUS'=>[['-','NOTEXPRESSION'],['~','NOTEXPRESSION'],['NOTEXPRESSION']],
         'NOTEXPRESSION'=>[['!','BINARYEXPRESSION'],['BINARYEXPRESSION']],
         'BINARYEXPRESSION'=>[['binary','INTERVALEXPRESSION'],['collate','const'],['INTERVALLEXPRESSION']],
         'INTERVALLEXPRESSION'=>[['interval','TIMEAMOUNT','TIMEUNIT'],['VALUEEXPRESSION']],
         'VALUEEXPRESSION'=>[['const'],['field','as','identifier'],['field'],['(','EXPRESSION',')','FUNCTION']],
         'FUNCT'=>['ident|(|LIST|)'],
    ];

    protected $stack = [];
   
    private function shift(\stdClass $token)
    {
        $token->terminal = true;
        $this->stack[] = $token;    
    }
    
    private function matches(array $right_hand_tokens): bool
    {
        $stack_size = count($this->stack);
        if (count($right_hand_tokens) > $stack_size) {
            return false; // Stack to small, can't fit
        }
        for ($i = 0; $i < count($right_hand_tokens); $i++) {
            if ($right_hand_tokens[$i] !== $this->stack[$stack_size-count($right_hand_tokens)+$i]->type) {
                return false;
            }
        }
        return true;
    }
    
    private function canReduce(): bool
    {
        foreach ($this::GRAMMAR as $left_hand => $rules) {
            foreach ($rules as $right_hand) {
                if ($this->matches($right_hand)) {
                    return [$left_hand => $right_hand];
                }
            }
        }
        return false;
    }
    
    private function reduce(\stdClass $rule)
    {
        $left_hand = array_keys($rule)[0];
        $right_hand = array_values($rule)[0];
        
    }
    
    private function shiftReducePart($lexer)
    {
        while ($token = $lexer->getNextToken()) {
            $this->shift($token);
            while ($rule = $this->canReduce()) {
                $this->reduce($rule);
            }
        }        
    }
    
    private function reducePart()
    {
        while ($rule = $this->canReduce()) {
            $this->reduce($rule);
        }        
    }
    
    private function validateStack()
    {
        if ((count($this->stack) > 1) || ($this->stack[0]->non_terminal !== 'FINAL')) {
            throw new InvalidStatementException("The input string was not parsable");
            // @todo Give some hints what went wrong
        }
    }
       
    public function parse(Lexer $lexer)
    {
        $this->stack = [];
        $this->shiftReducePart($lexer);
        $this->reducePart();
        $this->validateStack();
        return $this->stack[0];
    }
    
}  
