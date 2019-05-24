<?
class RolladensteuerungGroupMaster extends IPSModule {

	public function Create(){
		//Never delete this line!
		parent::Create();

		//These lines are parsed on Symcon Startup or Instance creation
		//You cannot use variables here. Just static values.

		$this->RegisterPropertyString("Shutters", "");

	}
	public function Destroy(){
		//Never delete this line!
		parent::Destroy();
	}
	public function ApplyChanges(){
		//Never delete this line!
		parent::ApplyChanges();
	}

	public function GetConfigurationForm() {
		$formdata = '{
                "elements":
                  [
                    {
                    "type": "List",
                    "name": "Shutters",
                    "caption": "Shutters",
                    "add": true,
                    "delete": true,
                    "sort": {
                        "column": "name",
                        "direction": "ascending"
                    },
                    "columns": [{
                        "label": "InstanceID",
                        "name": "instanceID",
                        "width": "75px",
                        "add": 0,
                        "edit": {
                            "type": "SelectInstance"
                        }
                    },{
                        "label": "Name",
                        "name": "Name",
                        "width": "auto",
                        "add": "Select Instance"
                    }],
                    "values": []
                }
                ]
                }
                ';
		$formdata = json_decode($formdata);

		if($this->ReadPropertyString("Shutters") != "") {
			//Annotate existing elements
			$shutters = json_decode($this->ReadPropertyString("Shutters"));
			foreach($shutters as $shutter) {
				//We only need to add annotations. Remaining data is merged from persistance automatically.
				//Order is determinted by the order of array elements
				if(IPS_ObjectExists($shutter->instanceID) && $shutter->instanceID !== 0) {
					$formdata->elements[0]->values[] = Array(
						"instanceID" => $shutter->instanceID, "Name" => IPS_GetName($shutter->instanceID),
					);
				}else {
					$formdata->elements[0]->values[] = Array(
						"$shutter->instanceID" => 0, "Name" => "Not found!",
					);
				}
			}
		}
		return json_encode($formdata);
	}
	public function GetGroupShutters(){
	    $shutters = json_decode($this->ReadPropertyString("Shutters"));
			foreach($shutters as $shutter) {
				if(IPS_ObjectExists($shutter->instanceID) && $shutter->instanceID !== 0) {
					$formdata->elements[0]->values[] = Array(
						"instanceID" => $shutter->instanceID, "Name" => IPS_GetName($shutter->instanceID),
					);
				}else {
					$formdata->elements[0]->values[] = Array(
						"$shutter->instanceID" => 0, "Name" => "Not found!",
					);
				}
			}
		if (!isset($formdata)){
		    throw new Exception("There are no shutters in the called group! Please add shutters to the group!");
		}
		return json_decode(json_encode($formdata),TRUE);
	}
	public function ConfigureShutterGroup(string $Property, int $Value){
	    IPS_LogMessage("function ConfigureShutterGroup", $this->InstanceID . " " . $Property . " " . $Value);

	    $shutters = $this->GetGroupShutters();

	    for ($i = 0; $i < sizeof($shutters["elements"][0]["values"]); $i++){
	        $ID = $shutters["elements"][0]["values"][$i]["instanceID"];
	        IPS_LogMessage("ConfigureShutterGroup", "Shutter: " . $ID);
	        IPS_SetProperty($ID, $Property, $Value);
	        IPS_ApplyChanges($ID);
        }
	}
	public function SetShutterGroupState(int $Value){
	    IPS_LogMessage("function SetShutterState", $this->InstanceID . " State" . $Value);

	    $shutters = $this->GetGroupShutters();

	    for ($i = 0; $i < sizeof($shutters["elements"][0]["values"]); $i++){
	        $ID = $shutters["elements"][0]["values"][$i]["instanceID"];
	        IPS_LogMessage("SetShutterState", "Shutter: " . $ID);
	        SetValue(IPS_GetObjectIDByIdent("State", $ID), $Value);
        }
	}
	public function SetShutterBrightness(int $Value){
	    IPS_LogMessage("function SetShutterBrightness", $this->InstanceID . " BrightnessBorder" . $Value);
	    $shutters = $this->GetGroupShutters();

	    for ($i = 0; $i < sizeof($shutters["elements"][0]["values"]); $i++){
	        $ID = $shutters["elements"][0]["values"][$i]["instanceID"];
	        IPS_LogMessage("SetShutterBrightness", "Shutter: " . $ID);
	        SetValue(IPS_GetObjectIDByIdent("BrightnessBorder", $ID), $Value);
        }
	}
}
