<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TrashController extends Controller
{
    private array $types = [
        'students' => ['model' => Student::class, 'label' => 'Tələbələr', 'nameField' => 'full_name'],
        'teachers' => ['model' => Teacher::class, 'label' => 'Müəllimlər', 'nameField' => 'name'],
        'groups' => ['model' => Group::class, 'label' => 'Qruplar', 'nameField' => 'name'],
        'enrollments' => ['model' => Enrollment::class, 'label' => 'Qeydiyyatlar (enrollment)', 'nameField' => null],
        'payments' => ['model' => Payment::class, 'label' => 'Ödənişlər', 'nameField' => null],
        'users' => ['model' => User::class, 'label' => 'İstifadəçilər', 'nameField' => 'name'],
    ];

    public function index(Request $request)
    {
        $activeType = $request->string('type')->value() ?: 'students';
        abort_unless(array_key_exists($activeType, $this->types), 404);

        $config = $this->types[$activeType];
        $counts = $this->trashCounts();

        /** @var class-string<Model> $modelClass */
        $modelClass = $config['model'];

        $items = $modelClass::onlyTrashed()
            ->with(['deleter' => fn ($q) => $q->withTrashed()])
            ->when($activeType === 'enrollments', fn ($q) => $q->with(['student' => fn ($q2) => $q2->withTrashed(), 'group' => fn ($q2) => $q2->withTrashed()]))
            ->when($activeType === 'payments', fn ($q) => $q->with(['enrollment' => fn ($q2) => $q2->withTrashed()->with(['student' => fn ($q3) => $q3->withTrashed(), 'group' => fn ($q3) => $q3->withTrashed()])]))
            ->orderByDesc('deleted_at')
            ->paginate(25)
            ->withQueryString();

        return view('trash.index', [
            'activeType' => $activeType,
            'types' => $this->types,
            'counts' => $counts,
            'items' => $items,
            'config' => $config,
        ]);
    }

    public function restore(Request $request, string $type, int $id)
    {
        $modelClass = $this->types[$type]['model'] ?? null;
        abort_unless($modelClass, 404);

        $item = $modelClass::onlyTrashed()->findOrFail($id);
        $item->restore();

        return back()->with('success', Str::headline($type) . ' bərpa edildi.');
    }

    public function forceDelete(Request $request, string $type, int $id)
    {
        $modelClass = $this->types[$type]['model'] ?? null;
        abort_unless($modelClass, 404);

        $item = $modelClass::onlyTrashed()->findOrFail($id);
        $item->forceDelete();

        return back()->with('success', 'Element birdəfəlik silindi — geri qaytarılmaz.');
    }

    private function trashCounts(): array
    {
        $counts = [];
        foreach ($this->types as $key => $config) {
            $modelClass = $config['model'];
            $counts[$key] = $modelClass::onlyTrashed()->count();
        }

        return $counts;
    }
}
