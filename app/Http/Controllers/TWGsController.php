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
                'text' => 'The Environmental Educators TWG aims to participate in developing and disseminating standardized training modules and educational resources to enhance the capacity of educators in teaching environmental concepts and promoting sustainability practices.',
                'responsibility' => [
                    'Participate in developing curriculum guidelines and teaching materials for environmental education programs.',
                    'Organize training workshops and seminars for environmental educators.',
                    'Participate in conducting research on best practices in environmental education and knowledge dissemination.',
                    'Foster collaboration with educational institutions, NGOs, and government agencies involved in environmental education.'
                ],
                'membership' => [
                    'Environmental educators from various sectors (schools, universities, NGOs, etc.).',
                    'Experts in curriculum development and environmental science.'
                ]
            ],
            [
                'name' => 'Water Resource Management',
                'text' => 'The Watershed Resource Management TWG aims to promote sustainable management of water resources and ecosystems within a blue economy framework, focusing on watershed management and conservation efforts.',
                'responsibility' => [
                    'Participate in developing strategies for integrated watershed management, considering ecological, social, and economic factors.',
                    'Identify opportunities for sustainable blue economy initiatives, such as eco-tourism and sustainable fisheries.',
                    'Propose for implementation of monitoring programs to assess the health and resilience of watersheds.',
                    'Facilitate stakeholder engagement and collaboration among government agencies, local communities, and businesses involved in watershed management.'
                ],
                'membership' => [
                    'Water resource managers and hydrologists.',
                    'Environmental NGOs and community-based organizations.',
                    'Representatives from industries with interests in water resources (e.g., agriculture, tourism, fisheries).',
                    'Academic researchers specializing in water management and blue economy.'
                ]
            ],
            [
                'name' => 'Waste Management',
                'text' => 'The Waste Management TWG aims to develop strategies and policies to promote waste reduction, recycling, and proper disposal practices, with a focus on achieving a circular economy.',
                'responsibility' => [
                    'Participate in developing guidelines for waste reduction, segregation, and recycling at the household, community, and industrial levels.',
                    'Promote public awareness campaigns on waste management and the importance of resource conservation.',
                    'Advocate for policy reforms to support sustainable waste management practices and infrastructure development.',
                    'Collaborate with industry stakeholders to implement extended producer responsibility (EPR) schemes and product stewardship initiatives.'
                ],
                'membership' => [
                    'Waste management experts and practitioners.',
                    'Representatives from recycling industries, waste collection companies, and landfill operators.',
                    'Environmental policymakers and regulators.',
                    'Community leaders and civil society organizations.'
                ]
            ],
            [
                'name' => 'Climate Change',
                'text' => 'The Climate Change TWG aims to provide expertise and guidance on climate change science, impacts, and adaptation strategies to inform decision-making and policy development.',
                'responsibility' => [
                    'Review and synthesize the latest scientific research on climate change and its effects on various sectors.',
                    'Develop climate risk assessment methodologies and tools for evaluating climate-related vulnerabilities.',
                    'Provide technical support to government agencies, businesses, and communities in developing climate adaptation and mitigation strategies.',
                    'Facilitate knowledge exchange and collaboration among climate scientists, policymakers, and stakeholders.'
                ],
                'membership' => [
                    'Climate scientists, meteorologists, and climate modelers.',
                    'Experts in climate change adaptation and resilience planning.'
                ]
            ],
            [
                'name' => 'Biodiversity / Natural Sciences',
                'text' => 'The Biodiversity/Natural Sciences TWG aims to advance understanding and conservation of biodiversity and ecosystems through research, monitoring, and advocacy efforts.',
                'responsibility' => [
                    'Participate in conducting biodiversity surveys and assessments to identify priority conservation areas and species.',
                    'Participate in developing management plans and conservation strategies for protected areas and biodiversity hotspots.',
                    'Monitor trends in biodiversity loss and ecosystem health, and recommend actions to mitigate threats.',
                    'Advocate for policies and regulations to protect biodiversity and promote sustainable land use practices.'
                ],
                'membership' => [
                    'Biologists, ecologists, and conservation scientists.',
                    'Representatives from conservation organizations and wildlife management agencies.',
                    'Indigenous and local community representatives with traditional ecological knowledge.',
                    'Experts in ecosystem services and natural resource management.'
                ]
            ],
            [
                'name' => 'Built Environment & Construction',
                'text' => 'The Built Environment & Construction TWG aims to promote sustainable practices in urban development and construction, with a focus on reducing environmental impacts and enhancing resilience.',
                'responsibility' => [
                    'Participate in developing green building standards and certification programs to promote energy efficiency and resource conservation.',
                    'Advocate for sustainable land use planning and urban design principles that prioritize walkability, green spaces, and mixed land use.',
                    'Provide technical assistance and capacity building to professionals in the construction industry on sustainable building techniques and materials.',
                    'Collaborate with government agencies and industry stakeholders to develop policies and incentives that promote sustainable construction practices.'
                ],
                'membership' => [
                    'Architects, engineers, and urban planners specializing in sustainable design.',
                    'Experts in green building certification and sustainable construction materials.'
                ]
            ],
            [
                'name' => 'Energy - Clean and Renewables',
                'text' => 'The Energy – Clean and Renewables TWG aims to accelerate the transition to clean and renewable energy sources, while promoting energy efficiency and decarbonization.',
                'responsibility' => [
                    'Participate in developing strategies to promote renewable energy deployment and energy efficiency improvements across various sectors.',
                    'Participate in conducting feasibility studies and cost-benefit analyses for clean energy projects, including solar, wind, hydro, and geothermal.',
                    'Advocate for policy reforms and regulatory incentives to support renewable energy investment and innovation.',
                    'Facilitate knowledge sharing and collaboration among energy stakeholders, including utilities, policymakers, and renewable energy developers.'
                ],
                'membership' => [
                    'Energy engineers, renewable energy specialists, and energy economists.',
                    'Representatives from utility companies, renewable energy associations, and research institutions.',
                    'Experts in energy modeling and grid integration of renewable resources.'
                ]
            ],
            [
                'name' => 'Environmental Policy & Governance',
                'text' => 'The Environmental Policy & Governance TWG aims to promote effective governance mechanisms and policy frameworks to address environmental challenges and promote sustainable development.',
                'responsibility' => [
                    'Monitor and analyze implementation of environmental policies and regulations at the county, national, and international levels.',
                    'Advocate for policy reforms to strengthen environmental protection, conservation, and sustainability.',
                    'Facilitate multi-stakeholder dialogues and partnerships to promote collaborative governance approaches.',
                    'Provide technical assistance and capacity building to government agencies and policymakers on environmental law and governance issues.'
                ],
                'membership' => [
                    'Environmental lawyers, policy analysts, and governance experts.',
                    'Experts in environmental diplomacy and international environmental agreements.'
                ]
            ],
            [
                'name' => 'Environmental Advocacy',
                'text' => 'The Environmental Advocacy Technical Working Group (TWG) is established to promote strategic advocacy on environmental and sustainability issues, with a focus on influencing policy, raising public awareness, and supporting active civic engagement in environmental governance.',
                'responsibility' => [
                    'Develop and implement advocacy strategies to advance environmental protection, climate action, and sustainable development at local and national levels.',
                    'Facilitate training, dialogue, and capacity-building initiatives for environmental activists, civil society actors, and community leaders.',
                    'Support public campaigns, grassroots movements, and community-based initiatives that address pressing environmental and sustainability challenges.',
                    'Advocate for inclusive and evidence-informed environmental policies that uphold social and ecological justice.'
                ],
                'membership' => [
                    'Environmental advocates, campaigners, and civil society organizations engaged in sustainability work.',
                    'Community-based organizations, youth-led movements, and advocacy networks focused on environmental justice.',
                    'Researchers, policy analysts, and subject-matter experts in environmental governance and public engagement.',
                    'Representatives from diverse sectors committed to advancing sustainability, including those working with marginalized and underrepresented groups.'
                ]
            ],
            [
                'name' => 'Environment, Health, and Safety (EHS)',
                'text' => 'The Environment, Health, and Safety (EHS) Technical Working Group (TWG) aims to promote integrated approaches that safeguard environmental quality, ensure occupational health and safety, and uphold public well-being through sound environmental practices and risk management frameworks.',
                'responsibility' => [
                    'Promote adherence to national and international EHS standards across sectors and institutions.',
                    'Support capacity development and technical guidance on EHS compliance, audits, and reporting procedures.',
                    'Facilitate knowledge exchange on emerging EHS risks, innovations, and best practices.',
                    'Collaborate with regulators, industry stakeholders, and training institutions to enhance EHS governance and accountability.'
                ],
                'membership' => [
                    'EHS professionals and compliance officers from public and private institutions.',
                    'Occupational health and safety experts, environmental engineers, and risk assessors.',
                    'Regulatory bodies, academic institutions, and training organizations specializing in EHS.',
                    'Organizations and consultants involved in workplace safety, environmental monitoring, and emergency preparedness.'
                ]
            ],
            [
                'name' => 'Environmental Impact Assessment (EIA)',
                'text' => 'The Environmental Impact Assessment (EIA) Technical Working Group (TWG) is established to uphold and strengthen the integrity, credibility, and technical rigor of Environmental Impact Assessment processes in Kenya. It seeks to harmonize practice, promote compliance with regulatory frameworks, and advance best-in-class methodologies that ensure environmental sustainability across all sectors of development.',
                'responsibility' => [
                    'Develop and disseminate standardized guidelines and tools for conducting EIAs and Environmental Audits (EAs) in alignment with national legislation and global best practices.',
                    'Facilitate peer review mechanisms and quality assurance systems to improve the robustness of EIA reports submitted by practitioners.',
                    'Engage in capacity-building initiatives targeting registered EIA experts, regulators, and project proponents.',
                    'Provide technical support on emerging trends such as Strategic Environmental Assessment (SEA), Social Impact Assessment (SIA), and any other matter affecting experts.',
                    'Contribute to national and county policy reforms that seek to improve the EIA/EA system, including licensing, monitoring, and enforcement.',
                    'Collaborate with NEMA, county governments, and sector-specific regulators to promote coordinated decision-making in the EIA process.'
                ],
                'membership' => [
                    'Licensed EIA/EA experts and lead experts registered with NEMA.',
                    'Academics and researchers specializing in environmental planning and impact assessment.',
                    'Officials from regulatory agencies and approving authorities.',
                    'Consultants and firms actively engaged in the preparation of EIA/EA reports.',
                    'Stakeholders with an interest in EIA policy reform, quality assurance, and monitoring.'
                ]
            ]
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
