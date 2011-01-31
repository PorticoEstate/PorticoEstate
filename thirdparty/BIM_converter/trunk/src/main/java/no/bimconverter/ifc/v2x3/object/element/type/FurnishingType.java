package no.bimconverter.ifc.v2x3.object.element.type;

import javax.xml.bind.annotation.XmlRootElement;

import no.bimconverter.ifc.v2x3.object.TypeObject;

import jsdai.SIfc2x3.EIfcdoorstyle;
import jsdai.lang.SdaiException;

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
