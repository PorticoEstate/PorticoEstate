package no.bimfm.ifc.v2x3;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import jsdai.SIfc2x3.EIfcpropertydefinition;
import jsdai.SIfc2x3.EIfcrelationship;
import jsdai.SIfc2x3.EIfcroot;
import jsdai.lang.EEntity;
import jsdai.lang.SdaiException;

public class IfcModelReport extends IfcModelImpl{
	private Map<String, String> report = new HashMap<String, String>(); 
	public IfcModelReport() {
		super();
	}
	public IfcModelReport(String ifcRepositoryName) {
		this();
		setIfcRepositoryName(ifcRepositoryName);
		this.initializeReport();
	}
	private void initializeReport() {
		this.openSessionAndGetModelInformation();
		this.addSizeToReport();
		this.addObjectCountToReport();
		this.addRelationsCountToReport();
		this.addResourceSchemaCountToReport();
		this.addPropertyDefinitionCountToReport();
	}
	public Map<String, String> getIfcModelReport() {
		return report;
	}
	
	private void addSizeToReport() {
		report.put("Model size", String.valueOf(this.size()));
	}
	private void addObjectCountToReport() {
		List<EEntity> objectList = this.getObjectsDefinitions();
		report.put("Object count", String.valueOf(objectList.size()));
	}
	private void addRelationsCountToReport() {
		this.openSessionAndGetAllInstancesOfType(EIfcrelationship.class);
		report.put("Relation count", String.valueOf(objectList.size()));
	}
	private void addResourceSchemaCountToReport() {
		this.openSessionAndGetAllInstancesOfType(EIfcroot.class);
		int objectCount = Integer.parseInt(this.report.get("Model size")) - objectList.size();
		report.put("Resource schema count", String.valueOf(objectCount));
	}
	private void addPropertyDefinitionCountToReport() {
		this.openSessionAndGetAllInstancesOfType(EIfcpropertydefinition.class);
		report.put("Property set count", String.valueOf(objectList.size()));
	}
	
	private void openSessionAndGetAllInstancesOfType(Class<? extends EEntity> ifcClass) {
		super.openSdaiSession();
		try {
			makeModelAvailable();
			this.objectList = getInstancesOfType(ifcClass);
		} catch (SdaiException e) {
			e.printStackTrace();
		} finally {
			super.closeSdaiSession();
		}
	}
	
	private void openSessionAndGetModelInformation() {
		super.openSdaiSession();
		try {
			makeModelAvailable();
			report.put("Model name", model.getName());
			report.put("Model id", model.getId());
			report.put("Model change date", model.getChangeDate());
		} catch (SdaiException e) {
			e.printStackTrace();
		} finally {
			super.closeSdaiSession();
		}
	}
	
	
	
}
