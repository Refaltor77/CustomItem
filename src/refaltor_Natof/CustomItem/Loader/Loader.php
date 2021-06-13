<?php

namespace refaltor_Natof\Customitem\Loader;

use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\network\mcpe\convert\ItemTranslator;
use pocketmine\network\mcpe\convert\ItemTypeDictionary;
use pocketmine\network\mcpe\protocol\types\ItemTypeEntry;
use pocketmine\utils\Config;
use refaltor_Natof\Customitem\Register;
use ReflectionObject;
use const pocketmine\RESOURCE_PATH;

class LoaderItem
{
    public static $data = [];
    public static $entries = [];
    public static $simpleNetToCoreMapping = [];
    public static $simpleCoreToNetMapping = [];

	public static function init(){

		$array = ["r16_to_current_item_map" => ["simple" => []], "item_id_map" => [], "required_item_list" => []];
		$config = new Config(Register::getInstance()->getDataFolder() . "config.yml", 2);

		foreach ($config->get("add") as $name => $id){
			$array['r16_to_current_item_map']['simple']["minecraft:$name"] = "minecraft:custom_$name";
			$array['item_id_map']["minecraft:$name"] = $id;
			$array['required_item_list']["minecraft:$name"] = ["runtime_id" => $id, "component_based" => false];
		}


		$data = file_get_contents(RESOURCE_PATH . '/vanilla/r16_to_current_item_map.json');
        $json = json_decode($data, true);
        $add = $array["r16_to_current_item_map"];
        $json["simple"] = array_merge($json["simple"], $add["simple"]);

        $legacyStringToIntMapRaw = file_get_contents(RESOURCE_PATH . '/vanilla/item_id_map.json');
        $add = $array["item_id_map"];
        $legacyStringToIntMap = json_decode($legacyStringToIntMapRaw, true);
        $legacyStringToIntMap = array_merge($add, $legacyStringToIntMap);

        /** @phpstan-var array<string, int> $simpleMappings */
        $simpleMappings = [];
        foreach($json["simple"] as $oldId => $newId){
            $simpleMappings[$newId] = $legacyStringToIntMap[$oldId];
        }
        foreach($legacyStringToIntMap as $stringId => $intId){
            $simpleMappings[$stringId] = $intId;
        }

        /** @phpstan-var array<string, array{int, int}> $complexMappings */
        $complexMappings = [];
        foreach($json["complex"] as $oldId => $map){
            foreach($map as $meta => $newId){
                $complexMappings[$newId] = [$legacyStringToIntMap[$oldId], (int) $meta];
            }
        }


        $old = json_decode(file_get_contents(RESOURCE_PATH  . '/vanilla/required_item_list.json'), true);
        $add = $array["required_item_list"];
        $table = array_merge($old, $add);
        $params = [];
        foreach($table as $name => $entry){
            $params[] = new ItemTypeEntry($name, $entry["runtime_id"], $entry["component_based"]);
        }
        self::$entries = $entries = (new ItemTypeDictionary($params))->getEntries();
        foreach($entries as $entry){
            $stringId = $entry->getStringId();
            $netId = $entry->getNumericId();
            if (isset($complexMappings[$stringId])){
                //
            }elseif(isset($simpleMappings[$stringId])){
                self::$simpleCoreToNetMapping[$simpleMappings[$stringId]] = $netId;
                self::$simpleNetToCoreMapping[$netId] = $simpleMappings[$stringId];
            }
        }
	}

	public static function register(){
		$data = new Config(Register::getInstance()->getDataFolder() . "config.yml", 2);
		foreach ($data->getAll()["add"] as $name => $id){
				if (!ItemFactory::isRegistered($id)){
					ItemFactory::registerItem(new Item($id, 0, $name));
					Item::addCreativeItem(new Item($id));
				}
		}
        self::init();
        $instance = ItemTranslator::getInstance();
        $ref = new ReflectionObject($instance);
        $r1 = $ref->getProperty("simpleCoreToNetMapping");
        $r2 = $ref->getProperty("simpleNetToCoreMapping");
        $r1->setAccessible(true);
        $r2->setAccessible(true);
        $r1->setValue($instance, self::$simpleCoreToNetMapping);
        $r2->setValue($instance, self::$simpleNetToCoreMapping);
	}
}
