package no.bimfm.ifc.v2x3.object.element.type;

import javax.xml.bind.annotation.XmlRootElement;

import jsdai.SIfc2x3.EIfcdoorstyle;
import jsdai.SIfc2x3.EIfcwindowstyle;
import jsdai.SIfc2x3.EIfcwindowstyleconstructionenum;
import jsdai.SIfc2x3.EIfcwindowstyleoperationenum;
import jsdai.lang.SdaiException;
import no.bimfm.ifc.v2x3.object.TypeObject;
@XmlRootElement
public class FurnishingType extends TypeObject{
	
	public void load(EIfcdoorstyle entity) {
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
	
	
}
