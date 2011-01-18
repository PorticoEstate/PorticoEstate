package no.bimfm.ifc.v2x3.object;

import java.util.List;
import java.util.Map;

import javax.xml.bind.annotation.XmlElement;
import javax.xml.bind.annotation.XmlElementWrapper;

import no.bimfm.jaxb.Attributes;
import no.bimfm.jaxb.BaseQuantities;
import no.bimfm.jaxb.BoundaryItem;
import no.bimfm.jaxb.ClassificationItem;
import no.bimfm.jaxb.NameValuePair;
import no.bimfm.jaxb.PropertyList;

import jsdai.SIfc2x3.EIfcobjectdefinition;
/*
 * Variables and methods common to all of the objects
 */
public interface CommonObject {
	public void load(EIfcobjectdefinition object);
	
	
	
		
	@XmlElementWrapper(name="properties")
	@XmlElement(name="propertySet") 
	public List<PropertyList> getProperties();

	public void setProperties(List<PropertyList> properties);
	
	public void setBaseQuantities(BaseQuantities baseQuantities);
	
	
	public BaseQuantities getBaseQuantities();
	
	@XmlElementWrapper(name="classification")
	@XmlElement(name="item") 
	public List<ClassificationItem> getClassificationList();

	public void setClassificationList(List<ClassificationItem> classificationList);
	
	@XmlElementWrapper(name="spaceBoundary")
	@XmlElement(name="boundary") 
	public List<BoundaryItem> getSpaceBoundary();

	public void setSpaceBoundary(List<BoundaryItem> spaceBoundary);

	
}
