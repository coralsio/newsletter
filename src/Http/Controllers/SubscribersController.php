<?php

namespace Corals\Modules\Newsletter\Http\Controllers;

use Corals\Foundation\Http\Controllers\BaseController;
use Corals\Modules\Newsletter\DataTables\SubscriberDataTable;
use Corals\Modules\Newsletter\Excel\SubscribersImport;
use Corals\Modules\Newsletter\Http\Requests\SubscriberImportRequest;
use Corals\Modules\Newsletter\Http\Requests\SubscriberRequest;
use Corals\Modules\Newsletter\Models\Subscriber;
use Maatwebsite\Excel\Facades\Excel;

class SubscribersController extends BaseController
{
    protected $excludedRequestParams = ['mail_lists'];

    public function __construct()
    {
        $this->resource_url = config('newsletter.models.subscriber.resource_url');

        $this->resource_model = new Subscriber();

        $this->title = 'Newsletter::module.subscriber.title';
        $this->title_singular = 'Newsletter::module.subscriber.title_singular';

        parent::__construct();
    }

    /**
     * @param SubscriberRequest $request
     * @param SubscriberDataTable $dataTable
     * @return mixed
     */
    public function index(SubscriberRequest $request, SubscriberDataTable $dataTable)
    {
        return $dataTable->render('Newsletter::subscribers.index');
    }

    /**
     * @param SubscriberRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(SubscriberRequest $request)
    {
        $subscriber = new Subscriber();

        $this->setViewSharedData(['title_singular' => trans('Corals::labels.create_title', ['title' => $this->title_singular])]);

        return view('Newsletter::subscribers.create_edit')->with(compact('subscriber'));
    }

    /**
     * @param SubscriberRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(SubscriberRequest $request)
    {
        try {
            $data = $request->except($this->excludedRequestParams);
            $mailLists = $request->get('mail_lists', []);
            $subscriber = Subscriber::create($data);
            $subscriber->mailLists()->sync($mailLists);

            flash(trans('Corals::messages.success.created', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Subscriber::class, 'store');
        }

        return redirectTo($this->resource_url);
    }

    /**
     * @param SubscriberRequest $request
     * @param Subscriber $subscriber
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(SubscriberRequest $request, Subscriber $subscriber)
    {
        $this->setViewSharedData(['title_singular' => trans('Corals::labels.show_title', ['title' => $subscriber->email])]);

        $this->setViewSharedData(['edit_url' => $this->resource_url . '/' . $subscriber->hashed_id . '/edit']);

        $subscriber->mail_lists_count = $subscriber->mailLists()->count();

        return view('Newsletter::subscribers.show')->with(compact('subscriber'));
    }

    /**
     * @param SubscriberRequest $request
     * @param Subscriber $subscriber
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(SubscriberRequest $request, Subscriber $subscriber)
    {
        $this->setViewSharedData(['title_singular' => trans('Corals::labels.update_title', ['title' => $subscriber->email])]);

        return view('Newsletter::subscribers.create_edit')->with(compact('subscriber'));
    }

    /**
     * @param SubscriberRequest $request
     * @param Subscriber $subscriber
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(SubscriberRequest $request, Subscriber $subscriber)
    {
        try {
            $data = $request->except($this->excludedRequestParams);

            $mailLists = $request->get('mail_lists', []);

            $subscriber->update($data);

            $subscriber->mailLists()->sync($mailLists);

            flash(trans('Corals::messages.success.updated', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Subscriber::class, 'update');
        }

        return redirectTo($this->resource_url);
    }

    /**
     * @param SubscriberRequest $request
     * @param Subscriber $subscriber
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(SubscriberRequest $request, Subscriber $subscriber)
    {
        try {
            $subscriber->delete();

            $message = ['level' => 'success', 'message' => trans('Corals::messages.success.deleted', ['item' => $this->title_singular])];
        } catch (\Exception $exception) {
            log_exception($exception, Subscriber::class, 'destroy');
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
        }

        return response()->json($message);
    }

    /**
     * @param SubscriberRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function importSubscribersView(SubscriberRequest $request)
    {
        $this->setViewSharedData([
            'title' => 'Newsletter::module.subscriber_import.title',
        ]);

        return view('Newsletter::subscribers.import.import_subscribers');
    }

    public function importSubscribers(SubscriberImportRequest $request)
    {
        $wrongCounter = 0;
        $successCounter = 0;

        try {
            Excel::import(new SubscribersImport(), $request->file('subscribers_file'));
        } catch (\Exception $exception) {
            log_exception($exception, Subscriber::class, 'importSubscribers');
        }

        return redirectTo($this->resource_url);
    }

    public function importSubscribersReport(SubscriberRequest $request, $action)
    {
        switch ($action) {
            case 'download':
                $file = session('import-subscribers-report');

                if (\File::exists($file)) {
                    return response()->download($file);
                }

                flash(trans('Newsletter::exception.subscribers.no_file'))->warning();

                return redirectTo($this->resource_url);

                break;
            case 'clear':
                @unlink(session('import-subscribers-report'));
                session()->forget('import-subscribers-report');

                return redirectTo($this->resource_url);

                break;
        }
    }
}
