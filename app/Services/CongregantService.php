<?php

namespace App\Services;

use App\Http\Requests\IndexCongregantRequest;
use App\Http\Requests\StoreCongregantRequest;
use App\Http\Requests\UpdateCongregantRequest;
use App\Models\Congregant;
use App\Traits\Services\HasBulkDelete;
use App\Traits\Services\HasCsvImportExport;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class CongregantService
{
    use HasBulkDelete, HasCsvImportExport;

    protected function getExportColumns(): array
    {
        return [
            'honorific_title',
            'full_name',
            'gender',
            'date_of_birth',
            'phone_number',
            'email',
            'date_of_baptism',
            'status',
        ];
    }

    protected function getExportFilename(): string
    {
        return 'jemaat_' . now()->format('Y-m-d') . '.csv';
    }

    protected function getTemplateFilename(): string
    {
        return 'template_jemaat.csv';
    }

    protected function getTemplateRows(): array
    {
        return [['bpk', 'Budi Santoso', 'male', '1990-01-15', '08123456789', 'budi@example.com', '2005-03-20', 'member']];
    }

    protected function writeExportRows($handle): void
    {
        Congregant::select(array_merge($this->getExportColumns(), ['id']))
            ->orderBy('full_name')
            ->chunkById(500, function ($congregants) use ($handle) {
                foreach ($congregants as $congregant) {
                    fputcsv($handle, [
                        $congregant->honorific_title?->value,
                        $congregant->full_name,
                        $congregant->gender,
                        $congregant->formatted_date_of_birth,
                        $congregant->phone_number,
                        $congregant->email,
                        $congregant->formatted_date_of_baptism,
                        $congregant->status,
                    ]);
                }
            });
    }

    public function getPaginatedCongregants(IndexCongregantRequest $request)
    {
        $validatedData = $request->validated();

        return Congregant::searchBy($validatedData)
            ->when($validatedData['status'] ?? null, function ($query) use ($validatedData) {
                $query->where('status', $validatedData['status']);
            })
            ->when($validatedData['gender'] ?? null, function ($query) use ($validatedData) {
                $query->where('gender', $validatedData['gender']);
            })
            ->select([
                'id',
                'full_name',
                'gender',
                'phone_number',
                'email',
                'status',
            ])
            ->paginate()
            ->withQueryString();
    }

    public function create(StoreCongregantRequest $request)
    {
        $data = $request->validated();
        $data['phone_number'] = $this->normalizePhoneNumber($data['phone_number'] ?? null);

        return Congregant::create($data);
    }

    public function update(UpdateCongregantRequest $request, int $id)
    {
        $congregant = Congregant::findOrFail($id, ['id']);

        $data = $request->validated();
        $data['phone_number'] = $this->normalizePhoneNumber($data['phone_number'] ?? null);

        $congregant->update($data);

        return $congregant;
    }

    public function delete(int $id)
    {
        Congregant::findOrFail($id, ['id'])->delete();
    }

    public function getCongregantsForAjax(Request $request)
    {
        return Congregant::query()
            // ->where('status', 'member')
            ->searchBy($request->all())
            ->doesntHave('serviceTypes')
            ->orderBy('full_name')
            ->select([
                'id',
                'full_name',
            ])
            ->simplePaginate();
    }

    public function importCsv(UploadedFile $file): array
    {
        [$handle, $earlyReturn] = $this->openValidatedCsvImport($file, 'congregants');
        if ($earlyReturn !== null) {
            return $earlyReturn;
        }

        $rules = (new StoreCongregantRequest())->rules();
        $messages = (new StoreCongregantRequest())->messages();

        $imported = 0;
        $failed = 0;
        $errors = [];
        $row = 1;

        while (($values = fgetcsv($handle)) !== false) {
            $row++;

            if (count($values) !== count($this->getExportColumns())) {
                $errors[] = __('congregants.import_row_column_mismatch', ['row' => $row]);
                $failed++;
                continue;
            }

            $data = array_combine($this->getExportColumns(), array_map('trim', $values));

            foreach (['honorific_title', 'date_of_birth', 'phone_number', 'email', 'date_of_baptism'] as $nullable) {
                if ($data[$nullable] === '') {
                    $data[$nullable] = null;
                }
            }

            $validator = Validator::make($data, $rules, $messages);
            $validator->after(fn($v) => StoreCongregantRequest::checkHonorificGender(
                $data['honorific_title'],
                $data['gender'],
                $v,
            ));

            if ($validator->fails()) {
                $messages_str = collect($validator->errors()->all())->implode(', ');
                $errors[] = __('congregants.import_row_error', ['row' => $row, 'errors' => $messages_str]);
                $failed++;
                continue;
            }

            $data['phone_number'] = $this->normalizePhoneNumber($data['phone_number']);

            Congregant::create($data);
            $imported++;
        }

        fclose($handle);

        return compact('imported', 'failed', 'errors');
    }

    private function normalizePhoneNumber(?string $phoneNumber)
    {
        if (! $phoneNumber) {
            return null;
        }

        try {
            $phoneNumberUtil = PhoneNumberUtil::getInstance();
            $phoneNumberProto = $phoneNumberUtil->parse($phoneNumber, 'ID');

            return $phoneNumberUtil->format($phoneNumberProto, PhoneNumberFormat::E164);
        } catch (NumberParseException $e) {
            return null;
        }
    }
}
