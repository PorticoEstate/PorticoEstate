package no.bimfm.ifc.v2x3.object.element.type;

import jsdai.SIfc2x3.EIfcwindowstyle;
import jsdai.SIfc2x3.EIfcwindowstyleconstructionenum;
import jsdai.SIfc2x3.EIfcwindowstyleoperationenum;
import jsdai.lang.SdaiException;
import no.bimfm.ifc.v2x3.object.TypeObject;

public class WindowStyle extends TypeObject{
	
	public void load(EIfcwindowstyle entity) {
		try {
			this.loadAttributes(entity);
			this.loadClassification(entity);
			this.loadMaterial(entity);
			this.loadTypeProperties(entity);
			//this.loadProperties(entity);
			/*
			this.loadClassification(entity);
			this.loadBaseQuantities(entity);
			this.loadProperties(entity);
			*/
		} catch (SdaiException e) {
			e.printStackTrace();
		}
	}
	
	private void loadAttributes(EIfcwindowstyle windowStyle) throws SdaiException {
		super.loadAttributes(windowStyle);
//		this.attributes.put(Attribute.CONSTRUCTION_TYPE.key, EIfcwindowstyleconstructionenum.toString(windowStyle.getConstructiontype(null)));
//		this.attributes.put(Attribute.OPERATION_TYPE.key, EIfcwindowstyleoperationenum.toString(windowStyle.getOperationtype(null)));
		this.attributes.setConstructionType(EIfcwindowstyleconstructionenum.toString(windowStyle.getConstructiontype(null)));
		this.attributes.setOperationType(EIfcwindowstyleoperationenum.toString(windowStyle.getOperationtype(null)));
	}
	
	public enum Attribute {
		CONSTRUCTION_TYPE("Construction type"),
		OPERATION_TYPE("Operation type");
		private final String key;
		Attribute(String key) {
	        this.key = key;
	    }
		public String getKey() {
			return key;
		}
	}

}
