<?php

namespace Sunhill\Framework\Crud;

class AbstractCrudEngine
{
    
    public function getElementCount(?string $filter): int
    {        
    }
    
    public function getColumns(): array
    {       
    }
    
    public function getColumnTitle(string $column): string
    {
        
    }
    
    public function getListEntries(int $offset, int $limit, ?string $order, ?string $order_dir, ?string $filter): array
    {
        
    }
    
    public function isSortable(string $column): bool
    {
        
    }
    
    public function getFilters(): array
    {
        
    }
}