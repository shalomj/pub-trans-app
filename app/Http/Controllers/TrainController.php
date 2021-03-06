<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\TrainRepositoryInterface;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\TrainFormRequest;

class TrainController extends Controller
{
    /**
     * Constructor
     * 
     * @param \App\Repositories\Contracts\TrainRepositoryInterface $repository
     */
    public function __construct(TrainRepositoryInterface $repository)
    {
        $this->middleware('auth');
        
        $this->repository = $repository;
    }

    /**
     * Display a listing of trains.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $trains = $this->repository->getTrainList();

        return view('trains.index', ['trains' => $trains]);
    }

    /**
     * Show the form for creating a new train.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $code = $this->repository->getNextCode();

        return view('trains.create', ['code' => $code]);
    }

    /**
     * Store a newly created train in storage.
     *
     * @param  \App\Http\Requests\TrainFormRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(TrainFormRequest $request)
    {
        if ($train = $this->repository->create($request->input())) {
            return ! $request->ajax() ? redirect()->route('trains.show', $train)
                                                  ->with('success', 'Train successfully added.')
                                      : response()->json([
                                            'status'    => 1,
                                            'message'   => 'Train successfully added.',
                                            'data'      => $train,
                                            'redirect'  => route('trains.show', $train),
                                      ]);
        }

        return ! $request->ajax() ? redirect()->route('trains.create')
                                              ->with('error', 'Unable to add train. Please try again.')
                                              ->withInput($request->input())
                                  : response()->json([
                                        'status'    => 0,
                                        'message'   => 'Unable to add train. Please try again.',
                                  ]);
    }

    /**
     * Display the specified train.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $train = $this->repository->getTrainWithSchedules($id);

        return view('trains.show', ['train' => $train]);
    }

    /**
     * Show the form for editing the specified train.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $train = $this->repository->find($id);

        return view('trains.edit', ['train' => $train]);
    }

    /**
     * Update the specified train in storage.
     *
     * @param  \App\Http\Requests\TrainFormRequest $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TrainFormRequest $request, $id)
    {
        if ( ! $this->repository->update($id, $request->input())) {
            return ! $request->ajax() ? redirect()->route('trains.edit', $id)
                                                  ->with('error', 'Unable to update train. Please try again.')
                                      : response()->json([
                                            'status'    => 0,
                                            'message'   => 'Unable to update train. Please try again.',
                                      ]);
        }

        return ! $request->ajax() ? redirect()->route('trains.show', $id)
                                              ->with('success', 'Train successfully updated.')
                                  : response()->json([
                                        'status'    => 1,
                                        'message'   => 'Train successfully updated',
                                        'redirect'  => route('trains.show', $id)
                                  ]);
    }

    /**
     * Remove the specified train from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
