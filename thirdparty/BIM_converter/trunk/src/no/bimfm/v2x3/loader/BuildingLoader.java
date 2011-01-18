package no.bimfm.v2x3.loader;

import java.util.ArrayList;
import java.util.List;

import no.bimfm.ifc.IfcSdaiException;
import no.bimfm.ifc.v2x3.object.Building;

import jsdai.SIfc2x3.EIfcbuilding;
import jsdai.lang.EEntity;
import jsdai.lang.SdaiModel;

/*
 * Loads objects of type building
 * According to the MV, there may be 1 or more buildings
 */
public class BuildingLoader extends CommonLoader {
	
	
	public BuildingLoader() {
	}
	
	public List<Building> loadBuildings(SdaiModel model) {
		List<Building> buildings = new ArrayList<Building>();
		List<EEntity> buildingsList = super.getEntitiesOfType(model, EIfcbuilding.class);
		if(buildingsList.size() == 0) {
			throw new IfcSdaiException("Error: No buildings found!");
		} else {
			for(EEntity buildingEntity: buildingsList) {
				Building currentBuilding = new Building();
				currentBuilding.load((EIfcbuilding)buildingEntity);
				buildings.add(currentBuilding);
			}
		}
		return buildings;
	}

	
}
