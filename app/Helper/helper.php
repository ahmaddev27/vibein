<?php
function categories()
{
    return[
        '1' => 'Coffee',
        '2' => 'Chocolate',
        '3' => 'Nuts',
        '4' => 'Drained fruits',
        '5' => 'Capsules',
        '6' => 'Flavors',
        '7' => 'Cups',
        '8' => 'Accessories',
    ];
}


function formatPagination($paginator)
{
    return [
        'current_page' => $paginator->currentPage(),
        'last_page' => $paginator->lastPage(),
        'per_page' => $paginator->perPage(),
        'total' => $paginator->total(),
    ];
}
