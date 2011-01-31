package no.bimconverter.ifc.v2x3.object;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;

import javax.xml.bind.annotation.XmlElement;
import javax.xml.bind.annotation.XmlElementWrapper;

import no.bimconverter.ifc.jaxb.BaseQuantities;
import no.bimconverter.ifc.jaxb.BoundaryItem;
import no.bimconverter.ifc.jaxb.ClassificationItem;
import no.bimconverter.ifc.jaxb.PropertyList;
import no.bimconverter.ifc.jaxb.owner.OwnerHistory;


import jsdai.SIfc2x3.AIfcrelassociates;
import jsdai.SIfc2x3.EIfcclassification;
import jsdai.SIfc2x3.EIfcclassificationreference;
import jsdai.SIfc2x3.EIfcobject;
import jsdai.SIfc2x3.EIfcobjectdefinition;
import jsdai.SIfc2x3.EIfcownerhistory;
import jsdai.SIfc2x3.EIfcproduct;
import jsdai.SIfc2x3.EIfcproject;
import jsdai.SIfc2x3.EIfcrelassociates;
import jsdai.SIfc2x3.EIfcrelassociatesclassification;
import jsdai.SIfc2x3.EIfcrelspaceboundary;
import jsdai.SIfc2x3.EIfcroot;
import jsdai.lang.A_integer;
import jsdai.lang.A_string;
import jsdai.lang.EEntity;
import jsdai.lang.SdaiException;
import jsdai.lang.SdaiIterator;

public class CommonObjectImpl extends CommonObjectDefinition implements CommonObject{
	
	protected List<PropertyList> properties = null;
	protected List<BoundaryItem> spaceBoundary = null;
	private BaseQuantities baseQuantities = null;
	private List<ClassificationItem> classificationList = null;
	private OwnerHistory ownerHistory;
	 
	public CommonObjectImpl() {
		super();
	}
	@Override
	public void load(EIfcobjectdefinition object){
		try {
			this.loadOwnerHistory(object);
			super.loadAttributes(object);
		} catch (SdaiException e) {
			e.printStackTrace();
		}
	}
	
	public enum Classification {
		ITEM_KEY("Item key"),
		ITEM_NAME("Item name"),
		SYSTEM_NAME("System name"),
		SYSTEM_ID("System id"),
		SYSTEM_EDITION("System edition");
		private final String key;
		Classification(String key) {
	        this.key = key;
	    }
		public String getKey() {
			return key;
		}
	}
	public enum Property {
		COMMON_PROPERTY("Common property");
		private final String key;
		Property(String key) {
	        this.key = key;
	    }
		public String getKey() {
			return key;
		}
	}
	
	
	
	
	

	protected void loadClassification(EIfcobjectdefinition site) throws SdaiException {
		
		AIfcrelassociates associationsAggregation = site.getHasassociations(null, null);
		SdaiIterator associationsIterator = associationsAggregation.createIterator();
		while(associationsIterator.next()) {
			EIfcrelassociates association = associationsAggregation.getCurrentMember(associationsIterator);
			if(association.isKindOf(EIfcrelassociatesclassification.class)) {
				EIfcrelassociatesclassification relAssociationsClassification = (EIfcrelassociatesclassification) association;
				
				EEntity relatingClassification = relAssociationsClassification.getRelatingclassification(null);
				//System.out.println(relatingClassification);
				if(relatingClassification.isKindOf(EIfcclassificationreference.class)){
					initializeClassification();
					ClassificationItem classificationItem = new ClassificationItem();
					EIfcclassificationreference classificationReference = (EIfcclassificationreference) relatingClassification;
					if(classificationReference.testItemreference(null))
						classificationItem.itemKey = classificationReference.getItemreference(null);
						//classification.put(ITEM_KEY.key, classificationReference.getItemreference(null));
					if(classificationReference.testName(null))
						classificationItem.itemName = classificationReference.getName(null);
						//classification.put(ITEM_NAME.key, classificationReference.getName(null));
					EIfcclassification referencedClassification = classificationReference.getReferencedsource(null);
					if(referencedClassification.testName(null))
						classificationItem.systemName = referencedClassification.getName(null);
						//classification.put(SYSTEM_NAME.key, referencedClassification.getName(null));
					/*
					if(referencedClassification.testSource(null)) {
						classification.put(SYSTEM_ID.key, referencedClassification.getSource(null));
					}
					*/
					if(referencedClassification.testEdition(null))
						classificationItem.systemEdition = referencedClassification.getEdition(null);
						//classification.put(SYSTEM_EDITION.key, referencedClassification.getEdition(null));
					this.classificationList.add(classificationItem);
					classificationItem = null;
				}
			}
		}
		//return classification;
	}

	private void initializeClassification() {
		if(this.classificationList == null) {
			this.classificationList =new ArrayList<ClassificationItem>();
		}
		
	}

	
	
	@XmlElementWrapper(name="properties")
	@XmlElement(name="propertySet") 
	public List<PropertyList> getProperties() {
		return properties;
	}

	public void setProperties(List<PropertyList> properties) {
		this.properties = properties;
	}

	protected void loadProperties(EIfcobject object) throws SdaiException {
		this.relateObjectPropertiesAndQuantities(object);
		populatePropertiesWithPropertyListObjects();
	}
	
	protected void loadOwnerHistory(EIfcroot theEntity) throws SdaiException {
		if(theEntity.testOwnerhistory(null)) {
			EIfcownerhistory ownerHistory = theEntity.getOwnerhistory(null);
			this.ownerHistory = new OwnerHistory().loadOwnerHistory(ownerHistory);
		}
	}

	protected void populatePropertiesWithPropertyListObjects() {
		if(propertiesList != null) {
			for( String key : propertiesList.keySet()) {
				initializeProperties();
				PropertyList plist = new PropertyList();
				plist.setName(key);
				Map<String, String> props = propertiesList.get(key);
				for(String subKey : props.keySet()) {
					//plist.getProperties().add(new NameValuePair(subKey, props.get(subKey)));
					plist.addElement(subKey, props.get(subKey));
				}
				this.properties.add(plist);
			}
		}
	}

	protected void initializeProperties() {
		if(this.properties == null) {
			this.properties = new ArrayList<PropertyList>();
		}
		
	}
	protected void setCommonProperty(String commonPropertyName) {
		if(this.properties != null) {
			for( PropertyList key : properties) {
				
				if(key.getName().equals(commonPropertyName)) {
					key.setName(CommonObjectImpl.Property.COMMON_PROPERTY.getKey());
				}
			}
		}
	}
	

	protected void loadBaseQuantities(EIfcobject entity) throws SdaiException {
		if(this.quantitiesList == null) {
			this.relateObjectPropertiesAndQuantities(entity);
		}
		if(this.quantitiesList !=null &&this.quantitiesList.size() > 0) {
			if(this.quantitiesList.containsKey("BaseQuantities")) {
				this.baseQuantities = new BaseQuantities();//new ArrayList<NameValuePair>();
				Map<String, String> baseQuantitiesMap  = this.quantitiesList.get("BaseQuantities");
				for(String key: baseQuantitiesMap.keySet()) {
					//this.baseQuantities.add(new NameValuePair(key, baseQuantitiesMap.get(key)));
					this.baseQuantities.addElement(key, baseQuantitiesMap.get(key));
				}
			} 
		}
	}

	public void setBaseQuantities(BaseQuantities baseQuantities) {
		this.baseQuantities = baseQuantities;
	}
	
	
	public BaseQuantities getBaseQuantities() {
		return baseQuantities;
	}
	
	@XmlElementWrapper(name="classification")
	@XmlElement(name="item") 
	public List<ClassificationItem> getClassificationList() {
		return classificationList;
	}

	public void setClassificationList(List<ClassificationItem> classificationList) {
		this.classificationList = classificationList;
	}

	protected BoundaryItem populateSpaceBoundaryItem(EIfcrelspaceboundary relationSpaceBoundary, EIfcproduct childElement)
			throws SdaiException {
					//EIfcelement relatedElement = relationSpaceBoundary.getRelatedbuildingelement(null);
					BoundaryItem bItem = new BoundaryItem();
					bItem.guid = childElement.getGlobalid(null);
					bItem.internalOrExternalBoundary = this.processInternalExternal(relationSpaceBoundary.getInternalorexternalboundary(null));
					bItem.physicalOrVirtualBoundary = this.processPhysicalOrVirtual(relationSpaceBoundary.getPhysicalorvirtualboundary(null));
					bItem.type = childElement.getInstanceType().getName(null);
					return bItem;
			}

	String processInternalExternal(int value) {
		if(value == 1) {
			return "Internal";
		} else if (value == 2) {
			return "External";
		} else {
			return "Undefined";
		}
	}

	String processPhysicalOrVirtual(int value) {
		if(value == 1) {
			return "Physical";
		} else if (value == 2) {
			return "Virtual";
		} else {
			return "Undefined";
		}
	}

	protected void initializeSpaceBoundary() {
		if ( this.spaceBoundary == null) {
			this.spaceBoundary = new ArrayList<BoundaryItem>();
		}
		
	}
	@XmlElementWrapper(name="spaceBoundary")
	@XmlElement(name="boundary") 
	public List<BoundaryItem> getSpaceBoundary() {
		return spaceBoundary;
	}

	public void setSpaceBoundary(List<BoundaryItem> spaceBoundary) {
		this.spaceBoundary = spaceBoundary;
	}
	
	public OwnerHistory getOwnerHistory() {
		return ownerHistory;
	}
	public void setOwnerHistory(OwnerHistory ownerHistory) {
		this.ownerHistory = ownerHistory;
	}
	
}
