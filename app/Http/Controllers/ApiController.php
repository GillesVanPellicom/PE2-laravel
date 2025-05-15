<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Employee;
use App\Models\Package;
use App\Models\PackageMovement;
use Exception;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\Router\Types\Node;

class ApiController extends Controller
{
    //
    public function getPackages(Request $request)
    {
        $validated = $request->validate([
            "courier" => 'required|integer'
        ]);

        $courier = User::role("courier")
            ->leftJoin("employees", "employees.user_id", "=", "users.id")
            ->where("employees.id", $validated["courier"])
            ->select("employees.id as employee_id", "users.first_name", "users.last_name")
            ->first();

        if (!$courier)
            return response()->json(["error" => "Invalid Employee ID"], 400);

        // $courier = Employee::join("users", "employees.user_id", "=", "users.id")
        //     ->where("employees.id", $validated["courier"])
        //     ->select("employees.id as employee_id", "users.first_name", "users.last_name")
        //     ->firstOrFail();

        $data = PackageMovement::where("package_movements.handled_by_courier_id", $validated["courier"])
            ->join('packages', "package_movements.package_id", "=", "packages.id")
            ->whereColumn("package_movements.current_node_id", "packages.current_location_id")
            ->leftJoin('package_movements as next_movements', 'package_movements.next_movement', '=', 'next_movements.id')
            ->select(
                "packages.id as package_id",
                "packages.reference",
                "next_movements.current_node_id as next_node",
                'package_movements.arrival_time'
            )
            ->get();

        if (!$data)
            return response()->json(["error" => "No packages found"], 400);
        
        foreach ($data as $package) {
            $node = Node::fromId($package["next_node"]);
            if (!$node)
                continue;

            $address = $node->getAddress();

            $nodeData = [
                "id" => $node->getID(),
                "description" => $node->getDescription(),
                "type" => $node->getType()->value,
                "address" => [
                    "id" => $address->id,
                    "city" => $address->city->name,
                    "postcode" => $address->city->postcode,
                    "street" => $address->street,
                    "house_number" => $address->house_number,
                    "bus_number" => $address->bus_number,
                    "country" => $address->city->country->country_name
                ]
            ];
            $package["next_node"] = $nodeData;
        }
        return response()->json(["courier" => $courier, "packages" => $data], 200, [], JSON_PRETTY_PRINT);
    }

    public function packageInfo(Request $request)
    {
        $validated = $request->validate([
            "package" => 'required|integer'
        ]);

        $package = Package::find($validated["package"])->first();

        if (!$package)
            return response()->json(["error" => "Package does not exist."], 400);

        try {
            $node = $package->getNextMovement();
        } catch (Exception $e){
            return response()->json(["error" => "Invalid Node ID."], 400);
        }

        $address = $node->getAddress();

        $nodeData = [
            "id" => $node->getID(),
            "description" => $node->getDescription(),
            "type" => $node->getType()->value,
            "address" => [
                "id" => $address->id,
                "city" => $address->city->name,
                "postcode" => $address->city->postcode,
                "street" => $address->street,
                "house_number" => $address->house_number,
                "bus_number" => $address->bus_number,
                "country" => $address->city->country->country_name
            ]
        ];

        $data = [
            "reference" => $package->reference,
            "next_node" => $nodeData
        ];

        return response()->json($data, 200, [], JSON_PRETTY_PRINT);
    }

    public function nodeInfo(Request $request)
    {
        $validated = $request->validate([
            "node" => 'required'
        ]);

        $node = Node::fromId($validated["node"]);

        if (!$node)
            return response()->json(["error" => "Invalid Node ID."], 400);


        $address = $node->getAddress();

        $nodeData = [
            "id" => $node->getID(),
            "description" => $node->getDescription(),
            "type" => $node->getType()->value,
            "address" => [
                "id" => $address->id,
                "city" => $address->city->name,
                "postcode" => $address->city->postcode,
                "street" => $address->street,
                "house_number" => $address->house_number,
                "bus_number" => $address->bus_number,
                "country" => $address->city->country->country_name
            ]
        ];

        return response()->json($nodeData, 200, [], JSON_PRETTY_PRINT);
    }

    public function addressInfo(Request $request)
    {
        $validated = $request->validate([
            "address" => "required|integer"
        ]);

        $address = Address::where("addresses.id", $validated["address"])
            ->leftJoin("cities", "addresses.cities_id", "=", "cities.id")
            ->leftJoin("countries", "cities.country_id", "=", "countries.id")
            ->select("street", "house_number", "bus_number", "cities.name as city", "cities.postcode as postcode", "countries.country_name as country")
            ->first();

        if (!$address)
            return response()->json(["error" => "Invalid Address ID."], 400);


        return response()->json($address, 400, [], JSON_PRETTY_PRINT);
    }
}
