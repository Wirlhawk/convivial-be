<?php

namespace App\Filament\Resources;

use App\Filament\Exports\TransaksiExporter;
use App\Filament\Resources\TransaksiResource\Pages;
use App\Models\DetailTransaksi;
use App\Models\JenisCucian;
use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Tabs\Tab;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class TransaksiResource extends Resource
{
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    protected static ?string $navigationBadgeTooltip = 'The number of new orders';
    protected static ?string $model = Transaksi::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart'; // Ikon terkait transaksi
    protected static ?string $activeNavigationIcon = 'heroicon-s-banknotes'; // Ikon terkait transaksi
    protected static ?string $pluralLabel = 'Transaksi'; // Override nama plural
    protected static ?string $label = 'Transaksi'; // Override nama singular
    protected static ?string $navigationLabel = 'Transaksi';
    protected static ?string $slug = 'kasir/transaksi';
    protected static ?string $breadcrumb = "Transaksi";
    protected static ?string $navigationGroup = 'Manajemen Keuangan'; // Kelompokkan dalam grup navi

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')
                    ->default(auth()->id()),
                Forms\Components\Wizard::make([
                    Step::make('Informasi Transaksi')
                        ->label(new HtmlString('<strong>Informasi Transaksi</strong>')) // Label den
                        ->description('Customer mana yang mau nyuci nih?') // Deskripsi step
                        ->completedIcon('heroicon-m-hand-thumb-up') // Ikon saat step selesai
                        ->schema([
                            Section::make('Informasi Transaksi')
                                ->description('Pilih nama customer yang akan mencuci')
                                ->icon('heroicon-m-user-group')
                                ->iconColor('primary')
                                ->schema(self::getInformasiTransaksi())
                                ->extraAttributes(['style' => 'box-shadow: 0 4px 6px -1px rgba(65, 158, 195, 0.3), 0 2px 4px -1px rgba(65, 158, 195, 0.2);']), // Box shadow
                        ]),
                    Step::make('Detail Transaksi')
                        ->label(new HtmlString('<strong>Detail Transaksi</strong>')) // Label den
                        ->description('Mau nyuci apa aja nih?') // Deskripsi step
                        ->completedIcon('heroicon-m-hand-thumb-up') // Ikon saat step selesai
                        ->schema([
                            Section::make('Detail Transaksi')
                                ->description('Masukkan detail transaksi berdasarkan jenis cucian yang dipilih')
                                ->icon('heroicon-m-shopping-bag')
                                ->iconColor('warning')
                                ->schema(self::getDetailTransaksi())
                                ->extraAttributes(['style' => 'box-shadow: 0 4px 6px -1px rgba(65, 158, 195, 0.3), 0 2px 4px -1px rgba(65, 158, 195, 0.2);']), // Box shadow
                        ]),
                    Step::make('Total & Estimasi')
                        ->label(new HtmlString('<strong>Total & Estimasi</strong>')) // Label den
                        ->description('Sip! Tinggal bayar, dan nunggu proses pencucian!') // Deskripsi step
                        ->completedIcon('heroicon-m-hand-thumb-up') // Ikon saat step selesai
                        ->schema([
                            Section::make('Total & Estimasi')
                                ->description('Lihat total harga dan estimasi selesai transaksi')
                                ->icon('heroicon-m-currency-dollar')
                                ->iconColor('success')
                                ->schema(self::getTotalDanEstimasi())
                                ->extraAttributes(['style' => 'box-shadow: 0 4px 6px -1px rgba(65, 158, 195, 0.3), 0 2px 4px -1px rgba(65, 158, 195, 0.2);']), // Box shadow
                        ]),
                ])
                    ->columnSpanFull()
                    ->nextAction(
                        fn(Action $action) => $action->label('Lanjut')->size('lg'),
                    )
                    ->previousAction(
                        fn(Action $action) => $action->label('Kembali'),
                    )
                    ->submitAction(new HtmlString(Blade::render(<<<BLADE
                <x-filament::button
                    type="submit"
                    size="md"
                >
                    Simpan Transaksi
                </x-filament::button>
            BLADE))),
            ]);
    }

    public static function getInformasiTransaksi(): array
    {
        return [
            Select::make('id_customer')
                ->relationship('customer', 'nama_customer')
                ->label('Customer')
                ->placeholder('Pilih customer atau tambah customer baru')
                ->required()
                ->searchable()
                ->preload()
                ->createOptionModalHeading('Tambah Customer Baru')
                ->createOptionForm([
                    Forms\Components\TextInput::make('nama_customer')
                        ->label('Nama Customer')
                        ->required()
                        ->maxLength(255),
                    PhoneInput::make('no_telp')
                        ->label('Nomor Telepon')
                        ->required()
                        ->onlyCountries(['ID', 'SG', 'MY', 'TH'])
                        ->defaultCountry('ID')
                        ->countrySearch(true)
                        ->showFlags(true)
                        ->formatAsYouType(true)
                        ->rules(['regex:/^[0-9+]*$/']),
                    Forms\Components\TextInput::make('alamat')
                        ->label('Alamat')
                        ->required(),
                    Select::make('tipe_customer')
                        ->placeholder('Pilih tipe customer')
                        ->label('Tipe Customer')
                        ->options([
                            'guest' => 'Guest',
                            'membership' => 'Membership',
                        ])
                        ->required(),
                ])
        ];
    }

    public static function getDetailTransaksi(): array
    {
        return [
            Repeater::make('DetailTransaksi')
                ->relationship('detailTransaksi')
                ->addActionLabel('Tambah Detail Transaksi lagi')
                ->addActionAlignment(Alignment::End)
                ->label('Detail Transaksi wajib diisi!')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Select::make('id_jenis_cucian')
                                ->label('Jenis Cucian')
                                ->relationship('jenisCucian', 'nama_jenis_cucian')
                                ->required()
                                ->reactive()
                                ->placeholder('Pilih jenis cucian')
                                ->afterStateUpdated(function (callable $set, callable $get) {
                                    $jenisCucian = JenisCucian::find($get('id_jenis_cucian'));
                                    $hargaperTimbangan = $jenisCucian?->harga_per_timbangan ?? 0;
                                    $idJenisTimbangan = $jenisCucian?->id_jenis_timbangan ?? null;
                                    $namaJenisTimbangan = $jenisCucian?->jenisTimbangan->nama_jenis_timbangan ?? 'Tidak Diketahui';
                                    $set('harga_per_timbangan', $hargaperTimbangan);
                                    $set('nama_jenis_timbangan', $namaJenisTimbangan);
                                    $set('id_jenis_timbangan', $idJenisTimbangan);
                                    $berat = $get('berat');
                                    $set('sub_total', ($berat ?? 0) * $hargaperTimbangan);
                                }),
                            TextInput::make('nama_jenis_timbangan')
                                ->label('Jenis Timbangan')
                                ->readonly()
                                ->suffixIcon('heroicon-o-scale')
                                ->suffixIconColor('primary')
                                ->placeholder('Jenis timbangan otomatis terisi'),
                        ]),
                    Grid::make(3)
                        ->schema([
                            TextInput::make('berat')
                                ->label('Berat (kg) atau Satuan')
                                ->placeholder('Isi berat dalam (Kg) atau Satuan pakaian')
                                ->numeric()
                                ->required()
                                ->reactive()
                                ->inputMode('decimal')
                                ->suffixIcon('heroicon-o-scale')
                                ->suffixIconColor('primary')
                                ->extraInputAttributes(['class' => 'text-gray-700 font-medium'])
                                ->afterStateUpdated(function (callable $set, callable $get) {
                                    $berat = floatval($get('berat'));
                                    $hargaperTimbangan = floatval($get('harga_per_timbangan'));
                                    $set('sub_total', $berat * $hargaperTimbangan);
                                }),
                            Group::make()
                                ->schema([
                                    TextInput::make('harga_per_timbangan')
                                        ->label('Harga per Timbangan')
                                        ->placeholder('Harga per timbangan otomatis terisi')
                                        ->readonly()
                                        ->numeric()
                                        ->prefix('Rp')
                                        ->suffixIcon('heroicon-m-currency-dollar')
                                        ->suffixIconColor('success')
                                        ->extraInputAttributes(['class' => 'text-green-600 font-bold'])
                                        ->afterStateHydrated(function (callable $set, callable $get) {
                                            $hargaPerTimbangan = $get('harga_per_timbangan');
                                            $formattedHargaPerTimbangan = number_format($hargaPerTimbangan, 0, ',', '.');
                                            $set('harga_per_timbangan_formatted', $formattedHargaPerTimbangan);
                                        }),
                                    Placeholder::make('harga_per_timbangan_formatted')
                                        ->label('')
                                        ->content(
                                            fn(callable $get) =>
                                            $get('harga_per_timbangan') ? 'Rp ' . number_format($get('harga_per_timbangan'), 0, ',', '.') : ''
                                        )
                                        ->hidden(fn(callable $get) => !$get('harga_per_timbangan'))
                                        ->extraAttributes(['class' => 'text-sm text-gray-500 -mt-3']),
                                ]),
                            Group::make()
                                ->schema([
                                    TextInput::make('sub_total')
                                        ->label('Sub Total')
                                        ->placeholder('Sub Total akan otomatis terisi')
                                        ->numeric()
                                        ->readonly()
                                        ->reactive()
                                        ->prefix('Rp')
                                        ->suffixIcon('heroicon-m-currency-dollar')
                                        ->suffixIconColor('primary')
                                        ->extraInputAttributes(['class' => 'text-blue-600 font-bold text-lg'])
                                        ->afterStateHydrated(function (callable $set, callable $get) {
                                            $subTotal = $get('sub_total');
                                            $formattedSubTotal = number_format($subTotal, 0, ',', '.');
                                            $set('sub_total_formatted', $formattedSubTotal);
                                        }),
                                    Placeholder::make('sub_total_formatted')
                                        ->label('')
                                        ->content(
                                            fn(callable $get) =>
                                            $get('sub_total') ? 'Rp ' . number_format($get('sub_total'), 0, ',', '.') : ''
                                        )
                                        ->hidden(fn(callable $get) => !$get('sub_total'))
                                        ->extraAttributes(['class' => 'text-sm text-gray-500 -mt-3']),
                                ]),
                        ]),
                    Hidden::make('id_jenis_timbangan')
                        ->default(fn(callable $get) => $get('jenisCucian')?->id_jenis_timbangan),
                ])
                ->columns(1)
                ->afterStateUpdated(function (callable $get, callable $set) {
                    $details = collect($get('DetailTransaksi'));

                    // Hitung total harga dan total berat KILOAN
                    $totalHargaSebelumDiskon = 0;
                    $totalBeratKiloan = 0;

                    foreach ($details as $detail) {
                        $isKiloan = strtolower($detail['nama_jenis_timbangan'] ?? '') === 'kiloan';
                        $berat = floatval($detail['berat'] ?? 0);
                        $harga = floatval($detail['harga_per_timbangan'] ?? 0);

                        $totalHargaSebelumDiskon += ($berat * $harga);

                        if ($isKiloan) {
                            $totalBeratKiloan += $berat;
                        }
                    }

                    $customer = \App\Models\Customer::find($get('id_customer'));
                    $diskon = 0;

                    if ($customer && $customer->tipe_customer === 'membership') {
                        $transactionCount = \App\Models\Transaksi::where('id_customer', $customer->id_customer)->count();
                        $isCreating = empty($get('id_transaksi'));

                        if ($isCreating) {
                            $transactionCount += 1;
                        }

                        // Diskon 10% jika total berat KILOAN >=12kg
                        if ($totalBeratKiloan >= 12) {
                            $diskon += 10;
                        }

                        // Diskon 10% jika transaksi >=8x
                        if ($transactionCount >= 8) {
                            $diskon += 10;
                        }
                    }

                    $set('total_harga', $totalHargaSebelumDiskon);
                    $set('diskon', $diskon);
                    $set(
                        'total_harga_setelah_diskon',
                        $totalHargaSebelumDiskon * ((100 - $diskon) / 100)
                    );
                })
                ->cloneable()
                ->defaultItems(1)
                ->reorderable()
                ->collapsible()
                ->grid(1)
                ->required(),
        ];
    }

    public static function getTotalDanEstimasi(): array
    {
        return [
            Grid::make()
                ->schema([
                    TextInput::make('total_harga')
                        ->label(function (callable $get) {
                            $customer = \App\Models\Customer::find($get('id_customer'));
                            return $customer && $customer->tipe_customer === 'membership'
                                ? 'Total Harga Sebelum Diskon'
                                : 'Total Harga';
                        })
                        ->readonly()
                        ->prefix('Rp')
                        ->numeric()
                        ->placeholder('Total harga sebelum diskon')
                        ->suffixIcon('heroicon-m-currency-dollar')
                        ->suffixIconColor('primary')
                        ->reactive()
                        ->columnSpan(2)
                        ->extraInputAttributes(['class' => 'text-red-600 font-bold text-lg'])
                        ->afterStateHydrated(function (callable $set, callable $get) {
                            $totalHarga = $get('total_harga');
                            $formattedTotalHarga = number_format($totalHarga, 0, ',', '.');
                            $set('total_harga_formatted', $formattedTotalHarga);
                        }),
                    TextInput::make('diskon')
                        ->label('Diskon (%)')
                        ->numeric()
                        ->default(0)
                        ->placeholder('Diskon akan dihitung otomatis')
                        ->readOnly()
                        ->reactive()
                        ->extraAttributes(['style' => 'margin-bottom: -1rem;'])
                        ->suffixIcon('heroicon-m-percent-badge')
                        ->suffixIconColor('warning')
                        ->extraInputAttributes([
                            'style' => 'color:#F59E0B; font-weight: 600;',
                            'class' => 'text-emerald-600 font-bold text-lg'
                        ])
                        ->columnSpan(1),
                    Placeholder::make('total_harga_formatted_label')
                        ->label('')
                        ->content(function (callable $get) {
                            $totalHarga = $get('total_harga');
                            if ($totalHarga) {
                                return 'Rp. ' . number_format($totalHarga, 0, ',', '.');
                            }
                            return '';
                        })
                        ->hidden(fn(callable $get) => !$get('total_harga'))
                        ->extraAttributes(['class' => 'text-sm text-gray-500 -mt-4'])
                        ->columnSpan(2),
                ])
                ->columns(3),
            Grid::make()
                ->schema([
                    TextInput::make('total_harga_setelah_diskon')
                        ->label('Total Harga Setelah Diskon')
                        ->readonly()
                        ->prefix('Rp')
                        ->numeric()
                        ->hidden(fn(callable $get) => \App\Models\Customer::find($get('id_customer'))?->tipe_customer !== 'membership')
                        ->placeholder('Total harga setelah diskon')
                        ->reactive()
                        ->suffixIcon('heroicon-m-currency-dollar')
                        ->suffixIconColor('success')
                        ->columnSpan(2)
                        ->extraInputAttributes(['style' => 'color: #22C55E', 'class' => 'font-bold text-lg'])
                        ->hidden(
                            fn(callable $get) =>
                            !($customer = \App\Models\Customer::find($get('id_customer'))) ||
                                $customer->tipe_customer !== 'membership'
                        )
                        ->afterStateHydrated(function (callable $set, callable $get) {
                            $totalHargaSetelahDiskon = $get('total_harga_setelah_diskon');
                            $formattedTotalHargaSetelahDiskon = number_format($totalHargaSetelahDiskon, 0, ',', '.');
                            $set('total_harga_setelah_diskon_formatted', $formattedTotalHargaSetelahDiskon);
                        }),
                    Forms\Components\DatePicker::make('estimasi_tanggal_pengambilan')
                        ->label('Estimasi Selesai')
                        ->displayFormat('dddd, D MMMM Y')
                        ->prefixIcon('heroicon-o-calendar')
                        ->prefixIconColor('secondary')
                        ->suffixIcon('heroicon-o-clock')
                        ->suffixIconColor('warning')
                        ->extraAttributes(['class' => 'font-medium text-purple-600'])
                        ->minDate(now())
                        ->required(),
                    Placeholder::make('total_harga_setelah_diskon_formatted')
                        ->extraAttributes(['style' => 'margin-top: -2rem;'])
                        ->hidden(
                            fn(callable $get) =>
                            !$get('id_customer') ||
                                !($customer = \App\Models\Customer::find($get('id_customer'))) ||
                                $customer->tipe_customer !== 'membership' ||
                                !$get('total_harga_setelah_diskon')
                        )
                        ->label('')
                        ->content(function (callable $get) {
                            $totalHargaSetelahDiskon = $get('total_harga_setelah_diskon');
                            return $totalHargaSetelahDiskon ? 'Rp ' . number_format($totalHargaSetelahDiskon, 0, ',', '.') : '';
                        })
                        ->hidden(fn(callable $get) => !$get('total_harga_setelah_diskon'))
                        ->extraAttributes(['class' => 'text-sm text-gray-500 -mt-3 ms-2'])
                        ->columnSpan(1),
                ])
                ->columns(3),
            Grid::make()
                ->schema([
                    Select::make('status_pembayaran')
                        ->label('Status Pembayaran')
                        ->options([
                            'belum_lunas' => 'Belum Lunas',
                            'lunas' => 'Lunas',
                        ])
                        ->required()
                        ->default('belum_lunas')
                        ->columnSpan(1),
                    Forms\Components\FileUpload::make('bukti_gambar')
                        ->label('Bukti Pembayaran')
                        ->image()
                        ->maxSize(2048)
                        ->directory('bukti_pembayaran')
                        ->columnSpan(1),
                ])
                ->columns(2),
        ];
    }


    public static function afterSave(Form $form, Transaksi $transaksi)
    {
        $user = auth()->user();
        $transaksi->update(['user_id' => $user->id]);

        $detailTransaksiData = $form->getState()['DetailTransaksi'];
        $totalHargaSebelumDiskon = 0;
        $totalBeratKiloan = 0;

        foreach ($detailTransaksiData as $detail) {
            $subTotal = ($detail['berat'] ?? 0) * ($detail['harga_per_timbangan'] ?? 0);
            $totalHargaSebelumDiskon += $subTotal;

            // Cek apakah jenis timbangan adalah KILOAN
            $isKiloan = strtolower($detail['nama_jenis_timbangan'] ?? '') === 'kiloan';
            if ($isKiloan) {
                $totalBeratKiloan += $detail['berat'];
            }
        }

        $customer = $transaksi->customer;
        $diskon = 0;

        if ($customer->tipe_customer === 'membership') {
            $transactionCount = \App\Models\Transaksi::where('id_customer', $customer->id_customer)->count();

            // Diskon 10% jika total berat KILOAN >=12kg
            if ($totalBeratKiloan >= 12) {
                $diskon += 10;
            }

            // Diskon 10% jika transaksi >=8x
            if ($transactionCount >= 8) {
                $diskon += 10;
            }
        }

        $transaksi->update([
            'total_harga' => $totalHargaSebelumDiskon,
            'total_harga_setelah_diskon' => $totalHargaSebelumDiskon * ((100 - $diskon) / 100),
            'diskon' => $diskon
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateIcon('heroicon-o-banknotes')
            ->emptyStateDescription('Setelah kamu membuat transaksi yang terkait, maka akan muncul di sini.')
            ->emptyStateHeading('Tidak ada transaksi terkait yang bisa ditampilkan')
            ->columns([
                TextColumn::make('id_transaksi')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-hashtag')
                    ->iconColor('primary-light')
                    ->label('ID Transaksi'),
                TextColumn::make('status_pembayaran')
                    ->label('Status Pembayaran')
                    ->icon('heroicon-o-banknotes')
                    ->iconColor(function ($state) {
                        $status = strtolower($state);

                        return match ($status) {
                            'lunas' => 'success',
                            'belum_lunas' => 'danger',
                            default => 'gray',
                        };
                    })
                    ->sortable()
                    ->formatStateUsing(fn($state) => ucwords(str_replace('_', ' ', strtolower($state)))),
                TextColumn::make('customer.nama_customer')
                    ->label('Customer')
                    ->icon('heroicon-o-user')
                    ->iconColor('secondary')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('estimasi_tanggal_pengambilan')
                    ->label('Estimasi Pengambilan')
                    ->icon('heroicon-o-calendar-days')
                    ->iconColor(function ($state) {
                        $estimasiTanggal = \Carbon\Carbon::parse($state);
                        $now = \Carbon\Carbon::now();
                        $diffInDays = $now->diffInDays($estimasiTanggal, false); // Selisih hari (negatif jika sudah lewat)

                        if ($diffInDays == 1) { // H-1
                            return 'danger'; // Warna merah
                        } elseif ($diffInDays == 3) { // H-2
                            return 'warning'; // Warna kuning
                        } elseif ($diffInDays == 4 || $diffInDays == 6) { // H-3 atau H-5
                            return 'primary'; // Warna biru
                        } else {
                            return 'primary'; // Default
                        }
                    })
                    ->sortable()
                    ->searchable()
                    ->date('d F Y'), // Format tanggal
                TextColumn::make('total_harga')->label('Total Harga')
                    ->sortable()
                    ->icon('heroicon-o-banknotes')
                    ->iconColor('success')
                    ->searchable()
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                // Kolom untuk tanggal dari created_at
                TextColumn::make('created_at_date')
                    ->icon('heroicon-o-calendar')
                    ->label('Tanggal Dibuat') // Label untuk kolom tanggal
                    ->getStateUsing(fn($record) => $record->created_at?->format('d F Y')) // Format hanya tanggal
                    ->sortable(),

                // Kolom untuk waktu dari created_at
                TextColumn::make('created_at_time')
                    ->label('Waktu Dibuat') // Label untuk kolom waktu
                    ->getStateUsing(fn($record) => $record->created_at?->format('H:i')) // Format hanya waktu
                    ->description(fn($record) => Carbon::parse($record->created_at)->locale('id')->diffForHumans()) // Deskripsi dalam bahasa Indonesia
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Petugas')
                    ->icon('heroicon-o-user-circle')
                    ->iconColor('secondary')
                    ->sortable()
                    ->searchable(),
            ])
            ->defaultSort('id_transaksi', 'desc')
            ->filters([
                // Filter berdasarkan tanggal created_at
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Dari Tanggal')
                            ->placeholder('Pilih tanggal awal'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Sampai Tanggal')
                            ->placeholder('Pilih tanggal akhir'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Dari tanggal: ' . \Carbon\Carbon::parse($data['created_from'])->format('d F Y');
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Sampai tanggal: ' . \Carbon\Carbon::parse($data['created_until'])->format('d F Y');
                        }
                        return $indicators;
                    }),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'diterima' => 'Diterima',
                        'diproses' => 'Diproses',
                        'selesai' => 'Selesai',
                        'diambil' => 'Diambil',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['value'])) {
                            $query->where('status', $data['value']);
                        }
                    }),
            ])
            ->actions([

                Tables\Actions\ActionGroup::make([ // ActionGroup updated with ellipsis icon
                    Tables\Actions\ViewAction::make('view_data')
                        ->label('View Data') // Label for button
                        ->icon('heroicon-o-eye'), // Eye icon)

                    Tables\Actions\EditAction::make(), // Edit action
                    Tables\Actions\Action::make('edit_payment_status')
                        ->label('Edit Status Pembayaran')
                        ->icon('heroicon-o-credit-card')
                        ->action(function ($record, array $data) {
                            $record->update(['status_pembayaran' => $data['status_pembayaran']]);
                        })
                        ->form([
                            Select::make('status_pembayaran')
                                ->label('Status Pembayaran')
                                ->options([
                                    'belum_dibayar' => 'Belum Dibayar',
                                    'lunas' => 'Lunas',
                                    'dp' => 'DP (Down Payment)',
                                ])
                                ->default(fn($record) => $record->status_pembayaran)
                                ->required(),
                        ])
                        ->requiresConfirmation()
                        ->modalDescription(fn(Transaksi $record) => "Anda sedang mengubah status pembayaran untuk transaksi {$record->id_transaksi}")
                        ->modalHeading(fn(Transaksi $record) => "Status Pembayaran - Invoice {$record->id_transaksi}")
                        ->modalSubmitActionLabel('Simpan Perubahan')
                        ->modalCancelActionLabel('Batal')
                        ->modalWidth('md'),


                    Tables\Actions\Action::make('edit_status')
                        ->label('Edit Status') // Label untuk tombol
                        ->icon('heroicon-o-pencil')
                        ->action(function ($record, array $data) { // Logika saat tombol ditekan
                            $record->update(['status' => $data['status']]);
                        })
                        ->form([
                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'diterima' => 'Diterima',
                                    'diproses' => 'Diproses',
                                    'selesai' => 'Selesai',
                                    'diambil' => 'Diambil',
                                ])
                                ->default(fn($record) => $record->status) // Mengatur status yang dipilih sesuai dengan status saat ini
                                ->required(),
                        ])
                        ->requiresConfirmation()
                        ->modalDescription(fn(Transaksi $record) => "Kamu sedang mengubah data transaksi $record->id_transaksi")
                        ->modalHeading(fn(Transaksi $record) => "Invoice $record->id_transaksi")
                        ->modalSubmitActionLabel('Simpan Perubahan')
                        ->modalCancelActionLabel('Batal')
                        ->modalWidth('md'), // Ukuran modal (opsional)
                    // Action baru untuk cetak invoice
                    Tables\Actions\Action::make('download_invoice')
                        ->label('Unduh Invoice')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->url(function (Transaksi $record) {
                            return URL::signedRoute(
                                'invoices.download',
                                ['transaksi' => $record->id_transaksi],
                                now()->addMinutes(15) // Expire dalam 15 menit
                            );
                        })
                        ->openUrlInNewTab(),
                    Tables\Actions\Action::make('hapus')
                        ->label('Hapus') // Label tombol
                        ->color('danger') // Warna tombol
                        ->icon('heroicon-s-trash') // Ikon tombol
                        ->requiresConfirmation() // Tambahkan dialog konfirmasi
                        ->modalHeading('Konfirmasi Hapus') // Judul dialog
                        ->modalSubheading(fn(Transaksi $record) => "Apakah kamu yakin ingin menghapus {$record->id_transaksi} ?") // Pesan tambahan dengan bold dan space)
                        ->modalSubmitActionLabel('Hapus')
                        ->modalCancelActionLabel('Batal')
                        ->action(function ($record) {
                            $recipient = auth()->user();
                            Notification::make()
                                ->title("Transaksi berhasil dihapus.")
                                ->success()
                                ->send()
                                ->sendToDatabase($recipient);
                            $record->delete(); // Aksi penghapusan
                        }),
                ])->label('Aksi Transaksi') // Set group label
                    ->icon('heroicon-o-ellipsis-vertical') // Icon titik tiga untuk pengaturan
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Hapus Data') // Opsional: Menyesuaikan label
                    ->icon('heroicon-o-trash')
                    ->color('danger') // Warna merah untuk indikasi penghapusan
                    ->requiresConfirmation()
                    ->modalDescription(fn(Transaksi $record) => "Apakah kamu yakin ingin menghapus semua data transaksi ?")
                    ->modalSubmitActionLabel('Hapus')
                    ->modalCancelActionLabel('Batal'),
                BulkAction::make('update_payment_status')
                    ->label('Ubah Status Pembayaran')
                    ->icon('heroicon-o-credit-card')
                    ->action(function (\Illuminate\Support\Collection $records, array $data) {
                        foreach ($records as $record) {
                            $record->update(['status_pembayaran' => $data['status_pembayaran']]);
                        }
                        $recipient = auth()->user();
                        \Filament\Notifications\Notification::make()
                            ->title('Status pembayaran berhasil diperbarui')
                            ->success()
                            ->sendToDatabase($recipient);
                    })
                    ->form([
                        Select::make('status_pembayaran')
                            ->label('Status Pembayaran')
                            ->options([
                                'belum_dibayar' => 'Belum Dibayar',
                                'lunas' => 'Lunas',
                                'dp' => 'DP (Down Payment)',
                            ])
                            ->required()
                            ->placeholder('Pilih status pembayaran'),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Ubah Status Pembayaran')
                    ->modalSubmitActionLabel('Simpan Perubahan')
                    ->modalCancelActionLabel('Batal')
                    ->modalWidth('sm'),
                BulkAction::make('update_status') // Bulk Action for updating the status
                    ->label('Ubah Status') // Button label
                    ->icon('heroicon-o-pencil') // Button icon
                    ->action(function (\Illuminate\Support\Collection $records, array $data) { // Use Collection instead of array
                        foreach ($records as $record) {
                            $record->update(['status' => $data['status']]);
                        }
                        $recipient = auth()->user();
                        \Filament\Notifications\Notification::make()
                            ->title('Status berhasil diperbarui')
                            ->success() // Jenis notifikasi (success)
                            ->sendToDatabase($recipient); // Kirim notifikasi
                    })
                    ->form([ // Modal form for selecting a new status
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'diterima' => 'Diterima',
                                'diproses' => 'Diproses',
                                'selesai' => 'Selesai',
                                'diambil' => 'Diambil',
                            ])
                            ->required()
                            ->placeholder('Pilih status baru'),
                    ])
                    ->requiresConfirmation() // Confirmation before execution
                    ->modalHeading('Ubah Status Transaksi') // Modal title
                    ->modalSubmitActionLabel('Simpan Perubahan') // Submit button label
                    ->modalCancelActionLabel('Batal') // Cancel button label
                    ->modalWidth('xs'), // Modal size
                // Tambahkan Bulk Action untuk ekspor
                BulkAction::make('export')
                    ->label('Export Data')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (\Illuminate\Support\Collection $records) {
                        return TransaksiExporter::make()
                            ->query(fn() => Transaksi::whereIn('id_transaksi', $records->pluck('id_transaksi')))
                            ->fileName('transaksi_export_' . now()->format('Ymd_His'))
                            ->download();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Export Data Transaksi')
                    ->modalSubmitActionLabel('Export')
                    ->modalCancelActionLabel('Batal')
                    ->modalWidth('md'),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransaksis::route('/'),
            'create' => Pages\CreateTransaksi::route('/create'),
            'edit' => Pages\EditTransaksi::route('/{record}/edit'),
        ];
    }
}
