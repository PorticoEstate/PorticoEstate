package no.bimconverter.ifc.loader;

import java.util.ArrayList;
import java.util.List;

import no.bimconverter.ifc.IfcSdaiException;
import no.bimconverter.ifc.v2x3.object.BuildingStorey;

import jsdai.SIfc2x3.EIfcbuildingstorey;
import jsdai.lang.EEntity;
import jsdai.lang.SdaiModel;


public class BuildingStoreyLoader  extends CommonLoader {
	public BuildingStoreyLoader() {
	}
		
	public List<BuildingStorey> loadBuildingStoreys(SdaiModel model) {
		List<BuildingStorey> buildingStoreys = new ArrayList<BuildingStorey>();
		List<EEntity> buildingStoreyList = super.getEntitiesOfType(model, EIfcbuildingstorey.class);
		if(buildingStoreyList.size() == 0) {
			throw new IfcSdaiException("Error: No buildings storeys found!");
		} else {
			for(EEntity buildingStoreyEntity: buildingStoreyList) {
				BuildingStorey currentBuildingStorey = new BuildingStorey();
				currentBuildingStorey.load((EIfcbuildingstorey)buildingStoreyEntity);
				buildingStoreys.add(currentBuildingStorey);
			}
		}
		return buildingStoreys;
	}
}
