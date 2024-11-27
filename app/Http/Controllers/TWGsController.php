<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TWG;

class TWGsController extends Controller
{
    public function index(Request $request){
        $all = [
            [
            'name' => 'Environmental Educators',
            'text' => 'Focused on enhancing environmental literacy, this group empowers educators to promote sustainability through curriculum development, awareness programs, and capacity-building, fostering a knowledgeable and eco-conscious society.'
            ],
            [
            'name' => 'Watershed Catchment Management (Blue economy)',
            'text' => 'Dedicated to sustainable management of water resources, this group promotes conservation practices, rehabilitation, and sustainable use of watersheds and aquatic ecosystems to support Kenya’s growing blue economy.'
            ],
            [
            'name' => 'Sustainable Waste Management',
            'text' => 'This group addresses innovative approaches to managing waste through recycling, waste reduction, and circular economy practices, promoting sustainability and reducing environmental impact in urban and rural settings.'
            ],
            [
            'name' => 'Climate Science',
            'text' => 'Focused on understanding and addressing climate change, this group brings together professionals to advance climate research, mitigation strategies, and adaptation practices for a more resilient future.'
            ],
            [
            'name' => 'Biodiversity / Natural Sciences',
            'text' => 'This group works to conserve biodiversity and promote the sustainable use of natural resources, focusing on protecting ecosystems, wildlife, and enhancing conservation efforts across diverse habitats.'
            ],
            [
            'name' => 'Built Environment & Construction',
            'text' => 'Promoting sustainable practices in urban planning, architecture, and construction, this group aims to reduce the environmental footprint of infrastructure development while fostering green building initiatives.'
            ],
            [
            'name' => 'Clean Energy and Renewables',
            'text' => 'This group champions the transition to renewable energy sources, advocating for clean energy solutions such as solar, wind, and geothermal power to reduce carbon emissions and promote sustainability.'
            ],
            [
            'name' => 'Environmental Policy & Governance',
            'text' => 'Focused on shaping and influencing environmental policies, this group engages in advocacy, research, and collaboration with policymakers to strengthen governance and regulatory frameworks for environmental protection.'
            ],
            [
            'name' => 'Environmental Advocacy - Youth & Women Groups',
            'text' => 'Empowering youth and women in environmental leadership, this group focuses on advocacy, community engagement, and capacity-building to foster inclusive participation in sustainable development initiatives.'
            ],
        ];
        try{
            $user = $request->user();
            if ($user) {
                $twgs = TWG::where('user_id', $user->id)->first();
                return response()->json([
                    'all' => $all,
                    'twgs' => $twgs ? json_decode($twgs->twgs) : [],
                ], 200);
            } else {
                return response()->json([
                    'error' => 'Unauthorized',
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function join(Request $request){
        try{
            $user = $request->user();
            if ($user) {
                $twg = TWG::where('user_id', $user->id)->first();
                if ($twg) {
                    $groups = json_decode($twg->twgs);
                    throw_if(count($groups) >= 2, \Exception::class, 'You can only have 2 TWGs');
                    $groups[] = $request->twg;
                    $twg->twgs = json_encode($groups);
                    $twg->save();
                } else {
                    $twg = TWG::create($user->id, $request->twg);
                }
                return response()->json([
                    'message' => 'TWGs updated successfully',
                ], 200);
            } else {
                return response()->json([
                    'error' => 'Unauthorized',
                    'request' => $request->all(),
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function exit(Request $request){
        try{
            $user = $request->user();
            if ($user) {
                $twg = TWG::where('user_id', $user->id)->first();
                if ($twg) {
                    $groups = json_decode($twg->twgs);
                    $groups = array_filter($groups, function($group) use ($request){
                        return $group != $request->twg;
                    });
                    $twg->twgs = json_encode($groups);
                    $twg->save();
                }
                return response()->json([
                    'message' => 'TWGs updated successfully',
                ], 200);
            } else {
                return response()->json([
                    'error' => 'Unauthorized',
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ], 500);
        }
    }
    
}
