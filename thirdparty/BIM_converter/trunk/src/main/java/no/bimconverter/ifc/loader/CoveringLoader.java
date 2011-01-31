package no.bimconverter.ifc.loader;

import java.util.ArrayList;
import java.util.List;

import no.bimconverter.ifc.IfcSdaiException;
import no.bimconverter.ifc.v2x3.object.element.Covering;

import jsdai.SIfc2x3.EIfccovering;
import jsdai.lang.EEntity;
import jsdai.lang.SdaiModel;


public class CoveringLoader  extends CommonLoader {
	public CoveringLoader() {
	}
		
	public List<Covering> loadCoverings(SdaiModel model) {
		List<Covering> coverings = new ArrayList<Covering>();
		List<EEntity> coveringList = super.getEntitiesOfType(model, EIfccovering.class);
		if(coveringList.size() == 0) {
			throw new IfcSdaiException("Error: No buildings storeys found!");
		} else {
			for(EEntity coveringEntity: coveringList) {
				Covering currentCovering = new Covering();
				currentCovering.load((EIfccovering)coveringEntity);
				coverings.add(currentCovering);
			}
		}
		return coverings;
	}
}
