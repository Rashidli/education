<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Models\Group;
use App\Models\Teacher;
use App\Services\SettingsService;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $query = Group::query()->with('teacher')
            ->withCount(['enrollments as students_count' => fn ($q) => $q->where('is_active', true)]);

        if ($search = $request->string('search')->trim()->value()) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($teacherId = $request->integer('teacher_id')) {
            $query->where('teacher_id', $teacherId);
        }

        $groups = $query->latest()->paginate(15)->withQueryString();

        return view('groups.index', [
            'groups' => $groups,
            'teachers' => Teacher::where('is_active', true)->latest()->get(),
            'search' => $search,
            'teacherId' => $teacherId,
        ]);
    }

    public function create()
    {
        $teachers = Teacher::where('is_active', true)->latest()->get();

        return view('groups.create', [
            'group' => new Group(['is_active' => true]),
            'teachers' => $teachers,
            'defaultPrices' => [
                Teacher::TYPE_LOCAL => SettingsService::defaultPriceFor(Teacher::TYPE_LOCAL),
                Teacher::TYPE_FOREIGN => SettingsService::defaultPriceFor(Teacher::TYPE_FOREIGN),
            ],
        ]);
    }

    public function store(StoreGroupRequest $request)
    {
        Group::create($request->validated());

        return redirect()->route('groups.index')
            ->with('success', 'Qrup əlavə olundu.');
    }

    public function show(Group $group)
    {
        $group->load([
            'teacher',
            'enrollments' => fn ($q) => $q->latest(),
            'enrollments.student',
        ]);

        return view('groups.show', ['group' => $group]);
    }

    public function edit(Group $group)
    {
        return view('groups.edit', [
            'group' => $group,
            'teachers' => Teacher::where('is_active', true)->latest()->get(),
            'defaultPrices' => [
                Teacher::TYPE_LOCAL => SettingsService::defaultPriceFor(Teacher::TYPE_LOCAL),
                Teacher::TYPE_FOREIGN => SettingsService::defaultPriceFor(Teacher::TYPE_FOREIGN),
            ],
        ]);
    }

    public function update(UpdateGroupRequest $request, Group $group)
    {
        $group->update($request->validated());

        return redirect()->route('groups.index')
            ->with('success', 'Qrup yeniləndi.');
    }

    public function destroy(Group $group)
    {
        if ($group->enrollments()->exists()) {
            return back()->with('error', 'Bu qrupda tələbələr var. Əvvəlcə tələbələri çıxarın.');
        }

        $group->delete();

        return redirect()->route('groups.index')
            ->with('success', 'Qrup silindi.');
    }
}
