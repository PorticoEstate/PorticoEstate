package no.bimconverter.ifc.v2x3;

import static junit.framework.Assert.*;

import java.util.List;
import java.util.Map;

import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBElement;
import javax.xml.bind.JAXBException;
import javax.xml.bind.Marshaller;
import javax.xml.bind.Unmarshaller;

import jsdai.lang.EEntity;
import jsdai.lang.SdaiException;



import no.bimconverter.ifc.IfcTestMethods;
import no.bimconverter.ifc.Repositories;
import no.bimconverter.ifc.RepositoriesImpl;
import no.bimconverter.ifc.jaxb.Attributes;
import no.bimconverter.ifc.jaxb.Decomposition;
import no.bimconverter.ifc.jaxb.Units;
import no.bimconverter.ifc.v2x3.object.CommonObjectDefinition;
import no.bimconverter.ifc.v2x3.object.Project;

import org.junit.After;
import org.junit.Before;
import org.junit.Test;

public class IfcModelTest extends IfcTestMethods {
		
	int numberOfIfcElements = 1420;
	int numberOfIfcModels = 0;
	String ifcFilename = null;
	
	Repositories repo = null;
	
	@Before
	public void setUp() {
		super.createTestRepo();
	}
	@After
	public void tearDown() {
		
	}
	
	@Test
	public void testSetIfcModelName() {
		model.setIfcRepositoryName(testingRepository);
		assertTrue(model.getIfcRepositoryName().equals(testingRepository));
	}
	
	@Test
	public void testGetCurrentModelSize() {
		assertEquals(numberOfIfcElements, model.size());
	}
	
	@Test
	public void testGetObjectsFromModel() {
		
		List<EEntity> myList = model.getObjectsDefinitions();
		assertNotNull(myList);
	}
	
	@Test
	public void testCreateAndIntializeProjectObject() throws JAXBException {
		Project project = model.getProject();
		assertNotNull(project);
		this.testGetProjectUnits(project);
		this.testGetProjectAttributes(project);
		this.testGetProjectDecomposition(project);
	}
	
	private void testGetProjectUnits(Project project) {
		Units units = project.getUnits();
		Map<String,String> unitsMap = units.getElementMap();
		assertEquals("METRE", unitsMap.get("LENGTHUNIT"));
		assertEquals("DEGREE", unitsMap.get("PLANEANGLEUNIT"));
		assertEquals("SQUARE_METRE", unitsMap.get("AREAUNIT"));
		assertEquals("CUBIC_METRE", unitsMap.get("VOLUMEUNIT"));
		assertEquals(4, unitsMap.size());
	}
	

	private void testGetProjectAttributes(CommonObjectDefinition project) {
		//Map<String, String> units = project.getAttributes();
		/* for sample.ifc
		assertEquals("3MD_HkJ6X2EwpfIbCFm0g_", units.get(Project.ATTRIBUTE_KEY_GUID));
		assertEquals("Default Project", units.get(Project.ATTRIBUTE_KEY_NAME));
		assertEquals("#1 ModelTestRepository",units.get(Project.ATTRIBUTE_KEY_DESCRIPTION));
		assertNull(units.get(Project.ATTRIBUTE_KEY_LONGNAME));
		*/
		Attributes units = project.getAttributes();
		assertEquals("3KFKb0sfrDJwSHalGIQFZT", units.getGuid()); 
		assertEquals("FM-A-01", units.getName());
		assertNull(units.getDescription());
		assertNull(units.getPhase());
		assertEquals("FM Architectural Handover",units.getLongName());
		/*assertEquals("3KFKb0sfrDJwSHalGIQFZT", units.get(CommonObjectDefinition.ATTRIBUTE_KEY_GUID)); 
		assertEquals("FM-A-01", units.get(CommonObjectDefinition.ATTRIBUTE_KEY_NAME));
		assertNull(units.get(CommonObjectDefinition.ATTRIBUTE_KEY_DESCRIPTION));
		assertNull(units.get(Project.ATTRIBUTE_KEY_PHASE));
		assertEquals("FM Architectural Handover",units.get(Project.ATTRIBUTE_KEY_LONGNAME));*/
	}
	

	private void testGetProjectDecomposition(Project project) {
		//Map<String, String[]> units = project.getDecomposition();
		Decomposition decomposition = project.getDecomposition();
		/* for sample.ifc
		assertEquals("3rNg_N55v4CRBpQVbZJoHB", units.get(Project.DECOMPOSITION_KEY_SITE)[0]);
		assertEquals("0yf_M5JZv9QQXly4dq_zvI", units.get(Project.DECOMPOSITION_KEY_BUILDINGS)[0]);
		*/
		assertEquals("28hfXoRX9EMhvGvGhmaaad", decomposition.getSite());
		assertEquals(1, decomposition.getBuildings().size());
		assertEquals("28hfXoRX9EMhvGvGhmaaae", decomposition.getBuildings().get(0));
	}
	
	
}
