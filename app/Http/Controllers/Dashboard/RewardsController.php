<?php

namespace App\Http\Controllers\Dashboard;

use App\Reward;
use JavaScript;
use App\Merchant;
use Illuminate\Http\Request;
use App\CityCard\PhotoUploader;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\Controller;

class RewardsController extends Controller
{
    protected $photoUploader;

    public function __construct(PhotoUploader $photoUploader)
    {
        $this->photoUploader = $photoUploader;
    }

    public function index()
    {
    	$rewards = Reward::with('outlets:id,name')->latest()->get();

    	return view('dashboard.rewards.index', compact('rewards'));
    }

    public function create()
    {
        JavaScript::put([
            'merchants' => Merchant::orderBy('name', 'ASC')->get(),
            'adminPath'    => adminPath(),
        ]);

        return view('dashboard.rewards.create');
    }

    public function store(Request $request)
    {   
        try {
            $merchant = Merchant::findOrFail($request->merchant_id);
            $reward = Reward::create($request->all());

            // Assign the reward to the given outlets
            $outlets = collect(explode(',',$request->outlets));
        	$reward->outlets()->sync($outlets);

            if( ! empty($request->photo) )
            {
                // Upload Reward Photo
                $upload_path = sprintf('merchants/%s/rewards/%s', str_slug($merchant->name), str_slug($reward->title));
                $this->photoUploader
                    ->upload($reward, $request->photo, $upload_path)
                    ->createThumbnail()
                    ->save();
            }

        	return response()->json([
                'status'    => 1,
                'message'   => 'A reward has been successfully saved.'
            ]);
            
        } catch (Exception $e) {
            
        }
    }
}
