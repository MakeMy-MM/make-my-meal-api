<?php

namespace Tests\Unit\Domain\Recipe\Services;

use App\Domain\Recipe\DTOs\CreateRecipeDTO;
use App\Domain\Recipe\DTOs\FieldsRecipeDTO;
use App\Domain\Recipe\DTOs\FieldsRecipeIngredientDTO;
use App\Domain\Recipe\DTOs\FieldsRecipeStepDTO;
use App\Domain\Recipe\DTOs\UpdateRecipeDTO;
use App\Domain\Recipe\DTOs\UpdateRecipeIngredientItemDTO;
use App\Domain\Recipe\DTOs\UpdateRecipeStepItemDTO;
use App\Domain\Recipe\Models\Recipe;
use App\Domain\Recipe\Models\RecipeIngredient;
use App\Domain\Recipe\Models\RecipeStep;
use App\Domain\Recipe\Repositories\RecipeRepository;
use App\Domain\Recipe\Services\RecipeService;
use App\Domain\Recipe\Services\RecipeServiceInterface;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Unit\TestUnitCase;

class RecipeServiceTest extends TestUnitCase
{
    public function testCreateReturnsRecipe(): void
    {
        $this->mockTransaction();

        $step = $this->getFieldsRecipeStepDTO();
        $ingredient = $this->getFieldsRecipeIngredientDTO();
        $dto = $this->getCreateRecipeDTO([$step], [$ingredient]);
        $recipe = $this->getRecipe();
        $repository = $this->getRecipeRepository();

        $repository
            ->expects($this->once())
            ->method('create')
            ->with($dto)
            ->willReturn($recipe)
        ;

        $repository
            ->expects($this->once())
            ->method('createStep')
        ;

        $repository
            ->expects($this->once())
            ->method('createIngredient')
        ;

        $service = $this->getRecipeService($repository);
        $result = $service->create($dto);

        $this->assertSame($recipe, $result);
    }

    public function testCreateThrowsOnException(): void
    {
        $this->mockTransaction();

        $dto = $this->getCreateRecipeDTO();
        $repository = $this->getRecipeRepository();

        $repository
            ->expects($this->once())
            ->method('create')
            ->willThrowException(new \RuntimeException('DB error'))
        ;

        $this->expectException(\RuntimeException::class);

        $service = $this->getRecipeService($repository);
        $service->create($dto);
    }

    public function testDeleteCallsRepository(): void
    {
        $this->mockTransaction();

        $recipe = $this->getRecipe();
        $repository = $this->getRecipeRepository();

        $repository
            ->expects($this->once())
            ->method('delete')
            ->with($recipe)
        ;

        $service = $this->getRecipeService($repository);
        $service->delete($recipe);
    }

    public function testDeleteThrowsOnException(): void
    {
        $this->mockTransaction();

        $recipe = $this->getRecipe();
        $repository = $this->getRecipeRepository();

        $repository
            ->expects($this->once())
            ->method('delete')
            ->willThrowException(new \RuntimeException('DB error'))
        ;

        $this->expectException(\RuntimeException::class);

        $service = $this->getRecipeService($repository);
        $service->delete($recipe);
    }

    public function testUpdateWithoutChildrenReturnsRecipe(): void
    {
        $this->mockTransaction();

        $dto = $this->getUpdateRecipeDTO();
        $recipe = $this->getRecipe();
        $repository = $this->getRecipeRepository();

        $repository
            ->expects($this->once())
            ->method('update')
            ->with($dto)
            ->willReturn($recipe)
        ;

        $repository->expects($this->never())->method('deleteStepsByIds');
        $repository->expects($this->never())->method('deleteIngredientsByIds');

        $service = $this->getRecipeService($repository);
        $result = $service->update($dto);

        $this->assertSame($recipe, $result);
    }

    public function testUpdateWithStepsMergesCorrectly(): void
    {
        $this->mockTransaction();

        $existingStep = $this->getRecipeStep('step-1');
        $recipe = $this->getRecipe(steps: ['step-1' => $existingStep]);
        $repository = $this->getRecipeRepository();

        $updateItem = $this->getUpdateRecipeStepItemDTO(id: 'step-1', position: 1, description: 'Updated');
        $createItem = $this->getUpdateRecipeStepItemDTO(id: null, position: 2, description: 'New step');
        $dto = $this->getUpdateRecipeDTO(steps: [$updateItem, $createItem]);

        $repository
            ->expects($this->once())
            ->method('update')
            ->with($dto)
            ->willReturn($recipe)
        ;

        $repository->expects($this->once())->method('updateStep');
        $repository->expects($this->once())->method('createStep');
        $repository->expects($this->never())->method('deleteStepsByIds');

        $service = $this->getRecipeService($repository);
        $service->update($dto);
    }

    public function testUpdateWithStepsDeletesMissingIds(): void
    {
        $this->mockTransaction();

        $existingStep = $this->getRecipeStep('step-to-delete');
        $recipe = $this->getRecipe(steps: ['step-to-delete' => $existingStep]);
        $repository = $this->getRecipeRepository();

        $createItem = $this->getUpdateRecipeStepItemDTO(id: null, position: 1, description: 'Replacement');
        $dto = $this->getUpdateRecipeDTO(steps: [$createItem]);

        $repository
            ->expects($this->once())
            ->method('update')
            ->willReturn($recipe)
        ;

        $repository
            ->expects($this->once())
            ->method('deleteStepsByIds')
            ->with(['step-to-delete'])
        ;

        $repository->expects($this->once())->method('createStep');

        $service = $this->getRecipeService($repository);
        $service->update($dto);
    }

    public function testUpdateWithIngredientsMergesCorrectly(): void
    {
        $this->mockTransaction();

        $existingIngredient = $this->getRecipeIngredient('ing-1');
        $recipe = $this->getRecipe(ingredients: ['ing-1' => $existingIngredient]);
        $repository = $this->getRecipeRepository();

        $updateItem = $this->getUpdateRecipeIngredientItemDTO(id: 'ing-1', position: 1);
        $createItem = $this->getUpdateRecipeIngredientItemDTO(id: null, position: 2, ingredientId: 'new-ing', quantity: 3.0);
        $dto = $this->getUpdateRecipeDTO(ingredients: [$updateItem, $createItem]);

        $repository
            ->expects($this->once())
            ->method('update')
            ->willReturn($recipe)
        ;

        $repository->expects($this->once())->method('updateIngredient');
        $repository->expects($this->once())->method('createIngredient');
        $repository->expects($this->never())->method('deleteIngredientsByIds');

        $service = $this->getRecipeService($repository);
        $service->update($dto);
    }

    public function testUpdateWithIngredientDeletesMissingIds(): void
    {
        $this->mockTransaction();

        $existingIngredient = $this->getRecipeIngredient('ing-to-delete');
        $recipe = $this->getRecipe(ingredients: ['ing-to-delete' => $existingIngredient]);
        $repository = $this->getRecipeRepository();

        $createItem = $this->getUpdateRecipeIngredientItemDTO(id: null, position: 1, ingredientId: 'new-ing', quantity: 1.0);
        $dto = $this->getUpdateRecipeDTO(ingredients: [$createItem]);

        $repository
            ->expects($this->once())
            ->method('update')
            ->willReturn($recipe)
        ;

        $repository
            ->expects($this->once())
            ->method('deleteIngredientsByIds')
            ->with(['ing-to-delete'])
        ;

        $service = $this->getRecipeService($repository);
        $service->update($dto);
    }

    public function testUpdateThrowsOnException(): void
    {
        $this->mockTransaction();

        $dto = $this->getUpdateRecipeDTO();
        $repository = $this->getRecipeRepository();

        $repository
            ->expects($this->once())
            ->method('update')
            ->willThrowException(new \RuntimeException('DB error'))
        ;

        $this->expectException(\RuntimeException::class);

        $service = $this->getRecipeService($repository);
        $service->update($dto);
    }

    public function testGetByUserReturnsCollection(): void
    {
        $user = $this->getUser();
        $recipe = $this->getRecipe();
        $collection = new Collection([$recipe]);
        $repository = $this->getRecipeRepository();

        $repository
            ->expects($this->once())
            ->method('findByFields')
            ->with(
                $this->isInstanceOf(FieldsRecipeDTO::class),
                ['steps', 'recipeIngredients.ingredient'],
            )
            ->willReturn($collection)
        ;

        $service = $this->getRecipeService($repository);
        $result = $service->getByUser($user);

        $this->assertSame($collection, $result);
    }

    private function getRecipeService(
        ?RecipeRepository $repository = null,
    ): RecipeServiceInterface {
        return new RecipeService(
            $repository ?? $this->createStub(RecipeRepository::class),
        );
    }

    private function getRecipeRepository(): RecipeRepository&MockObject
    {
        return $this->createMock(RecipeRepository::class);
    }

    /**
     * @param array<int, FieldsRecipeStepDTO> $steps
     * @param array<int, FieldsRecipeIngredientDTO> $ingredients
     */
    private function getCreateRecipeDTO(
        array $steps = [],
        array $ingredients = [],
    ): CreateRecipeDTO&MockObject {
        $mock = $this->createMock(CreateRecipeDTO::class);
        $mock->method('getSteps')->willReturn($steps);
        $mock->method('getIngredients')->willReturn($ingredients);

        return $mock;
    }

    private function getFieldsRecipeStepDTO(): FieldsRecipeStepDTO&MockObject
    {
        $mock = $this->createMock(FieldsRecipeStepDTO::class);
        $mock->method('getPosition')->willReturn(1);
        $mock->method('getDescription')->willReturn('Step 1');

        return $mock;
    }

    private function getFieldsRecipeIngredientDTO(): FieldsRecipeIngredientDTO&MockObject
    {
        $mock = $this->createMock(FieldsRecipeIngredientDTO::class);
        $mock->method('getPosition')->willReturn(1);
        $mock->method('getIngredientId')->willReturn('ingredient-uuid');
        $mock->method('getQuantity')->willReturn(1.5);

        return $mock;
    }

    /**
     * @param array<int, UpdateRecipeStepItemDTO>|null $steps
     * @param array<int, UpdateRecipeIngredientItemDTO>|null $ingredients
     */
    private function getUpdateRecipeDTO(
        ?array $steps = null,
        ?array $ingredients = null,
    ): UpdateRecipeDTO&MockObject {
        $mock = $this->createMock(UpdateRecipeDTO::class);
        $mock->method('getSteps')->willReturn($steps);
        $mock->method('getIngredients')->willReturn($ingredients);
        $mock->method('hasSteps')->willReturn($steps !== null);
        $mock->method('hasIngredients')->willReturn($ingredients !== null);

        return $mock;
    }

    private function getUpdateRecipeStepItemDTO(
        ?string $id = null,
        int $position = 1,
        string $description = 'Step',
    ): UpdateRecipeStepItemDTO {
        return new UpdateRecipeStepItemDTO(
            id: $id,
            position: $position,
            description: $description,
        );
    }

    private function getUpdateRecipeIngredientItemDTO(
        ?string $id = null,
        int $position = 1,
        ?string $ingredientId = 'ingredient-uuid',
        ?float $quantity = 1.5,
    ): UpdateRecipeIngredientItemDTO {
        return new UpdateRecipeIngredientItemDTO(
            id: $id,
            position: $position,
            ingredientId: $ingredientId,
            quantity: $quantity,
        );
    }

    /**
     * @param array<string, RecipeStep&MockObject> $steps
     * @param array<string, RecipeIngredient&MockObject> $ingredients
     */
    private function getRecipe(
        string $id = 'recipe-uuid',
        array $steps = [],
        array $ingredients = [],
    ): Recipe&MockObject {
        $mock = $this->createConfiguredModelMock(Recipe::class, [
            'id' => $id,
            'steps' => new Collection($steps),
            'recipeIngredients' => new Collection($ingredients),
        ]);

        $mock->method('load')->willReturnSelf();

        return $mock;
    }

    private function getRecipeStep(
        string $id = 'step-uuid',
    ): RecipeStep&MockObject {
        $attributes = ['id' => $id];
        $mock = $this->createConfiguredModelMock(RecipeStep::class, $attributes);
        $mock->method('offsetExists')->willReturnCallback(fn(string $key) => isset($attributes[$key]));
        $mock->method('offsetGet')->willReturnCallback(fn(string $key) => $attributes[$key] ?? null);

        return $mock;
    }

    private function getRecipeIngredient(
        string $id = 'ingredient-uuid',
    ): RecipeIngredient&MockObject {
        $attributes = ['id' => $id];
        $mock = $this->createConfiguredModelMock(RecipeIngredient::class, $attributes);
        $mock->method('offsetExists')->willReturnCallback(fn(string $key) => isset($attributes[$key]));
        $mock->method('offsetGet')->willReturnCallback(fn(string $key) => $attributes[$key] ?? null);

        return $mock;
    }

    private function getUser(
        string $id = 'user-uuid',
    ): User&MockObject {
        return $this->createConfiguredModelMock(User::class, [
            'id' => $id,
        ]);
    }
}
