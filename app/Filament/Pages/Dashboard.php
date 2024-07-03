<?php

namespace App\Filament\Pages;

use App\Models\Default_answer;
use App\Models\Question;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Pages\Page;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.dashboard';

    private function makeOptions($question): array
    {
        $options = Default_answer::where('question_id', $question->id)->get();
        $arrayOption = [];
        foreach ($options as $opt) {
            $arrayOption[] = $opt->answer;
        }
        return $arrayOption;
    }
    private function makeSurwey(): array
    {

        $surwey = Question::all();

        // dd($surwey->count());

        $form = [];
        foreach ($surwey as $question) {

            if ($question->type_question === 'close') {

                $form[] = Select::make($question->id)
                    ->label($question->question_text)
                    ->options($this->makeOptions($question));
            } else {

                switch ($question->type_input_open) {

                    case 'date':
                        $form[] = DatePicker::make($question->id)
                            ->label($question->question_text)
                            ->required();
                        break;

                    default:
                        $Input = TextInput::make($question->id)
                            ->label($question->question_text)
                            ->required();
                        if ($question->type_input_open === 'numeric') {
                            $Input->numeric();
                        }

                        $form[] = $Input;

                        break;
                }
            }
        }

        // $wizard = [];

        // $steps = [];
        // // dd(count($form));
        // $cont = 1;
        // $block = 1;
        // $steps[] = Step::make('Bloque ' . $block)->columns(2);



        // foreach ($form as $input) {
        //     if ($cont === 3) {
        //         $schema[] = $input;
        //         $wizard[] = $steps[count($steps) - 1]->schema($schema)->columns(2);
        //         $block++;
        //         $steps[] = Step::make('Bloque ' . $block);
        //         $schema = [];
        //         $cont = 1;
        //     } else {
        //         $schema[] = $input;
        //         $cont++;
        //     }
        // }



        // dd(count($form));

        $wizard = [];
        $limit = 4;
        $step = [];
        $cont = 0;
        $contador = 1;
        foreach ($form as $input) {
            //preparar el step hasta que este listo para integrarce al wizard
            //cuantos inputs requieres por step
            $step[] = $input;
            if ($cont + 1 == $limit) { //estas en el limite ?
                $wizard[] = Step::make('Bloque ' . $contador)->schema($step)->columns(2);
                $cont = 0;
                $contador++;
                $step = [];
                continue;
            }

            $cont++;
        }
        //Si el step esta vacio no se requiere agregar al wizard
        // dd(count($step));
        if (count($step) !== 0)
            $wizard[] = Step::make('Bloque final')->schema($step)->columns(2);

        return [Wizard::make($wizard)];
    }


    protected function getHeaderActions(): array
    {
        return [
            Action::make('survey')
                ->label('Responder encuesta')
                ->form($this->makeSurwey())
                ->action(function ($data) {
                    dd($data);
                })
        ];
    }
}
