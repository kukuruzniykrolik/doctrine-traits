# Doctrine ORM Package with Traits for Routine Task Simplification

This is a simple package for `doctrine/orm` that allows you to use Traits to simplify certain routine tasks.

## WithTrait

Enables fetching entities along with others.

### Repository:

```php
class CategoryRepository extends ServiceEntityRepository
{
    use WithTrait; // <-- add trait

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }
}
```

Our service or controller
```php
class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
    ) {
    }

    public function index(): Response
    {
        $result = $this->categoryRepository
            ->with(['products', 'products.image']) // <-- load eager with products and products images
            ->getQuery()
            ->getResult();
        
        return $this->json($result);
    }
}
```

Also you can set other parameters for `with()`
```php
//code
$this->categoryRepository
    ->with(
        $fields, // Array of fields
        $join, // String of join type ['left', 'inner'] 
        $qb, // QueryBuilder
    )
//code
```
