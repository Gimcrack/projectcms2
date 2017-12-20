<?php

namespace {
    exit("This file is only for ide indexing");
}

namespace Illuminate\Support {

    use Tests\TestCase;

    /**
     * Class Collection
     *
     * @method static TestCase assertContains(mixed $value)
     * @method static TestCase assertEmpty()
     * @method static TestCase assertNotEmpty()
     * @method static TestCase assertCount(int $count)
     * @method static TestCase assertMinCount(int $count)
     *
     */
    class Collection
    {}
}

namespace Illuminate\Foundation\Testing {

    use Illuminate\Support\Collection;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Http\Resources\Json\Resource;
    use Illuminate\Http\Resources\Json\ResourceCollection;

    /**
     * Class TestResponse
     *
     * @method static TestResponse assertJsonMissingModel(Model $model)
     * @method static TestResponse assertJsonModel(Model $model)
     * @method static TestResponse assertJsonModelCollection(Collection $models)
     * @method static TestResponse assertJsonResource(Resource $resource)
     * @method static TestResponse assertJsonResourceCollection(ResourceCollection $collection)
     *
     */
    class TestResponse
    {}
}
