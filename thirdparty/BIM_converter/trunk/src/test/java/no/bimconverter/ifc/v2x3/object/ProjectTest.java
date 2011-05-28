package no.bimconverter.ifc.v2x3.object;

import static junit.framework.Assert.*;

import java.util.HashMap;
import java.util.List;

import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBException;
import javax.xml.bind.Marshaller;

import jsdai.lang.EEntity;
import jsdai.lang.SdaiException;



import no.bimconverter.ifc.IfcTestMethods;
import no.bimconverter.ifc.Repositories;
import no.bimconverter.ifc.RepositoriesImpl;
import no.bimconverter.ifc.jaxb.owner.OwnerHistory;
import no.bimconverter.ifc.jaxb.owner.Person;
import no.bimconverter.ifc.jaxb.owner.PersonAndOrganization;
import no.bimconverter.ifc.v2x3.IfcModelImpl;

import org.junit.After;
import org.junit.Before;
import org.junit.Ignore;
import org.junit.Test;

public class ProjectTest extends IfcTestMethods{
	int numberOfIfcElements = 1420;
	int numberOfIfcModels = 0;
	String ifcFilename = null;

	private Project project;
	
	
	@Before
	public void setUp() {
		super.createTestRepo();
		project = ((List<Project>) model.getFacilityManagementEntity(new Project())).get(0);
	}
	@After
	public void tearDown() {
	}
	
	@Test
	public void testProjectObject() throws JAXBException {
		//super.outputXmlToSystemOut(project);
		
		assertNotNull(project);
	}
	
	@Test
	public void testOwnerHistory() throws JAXBException {
		OwnerHistory ownerHistory = project.getOwnerHistory();
		PersonAndOrganization owningUser = ownerHistory.getOwningUser();
		Person thePerson = owningUser.getPerson();
		//super.outputXmlToSystemOut(ownerHistory);
		assertNotNull(ownerHistory);
	}
	@Test
	public void testTest() {
		HashMap<String, String> h1 = new HashMap<String, String>();
		h1.put("1", "2");
		h1.put("2", "1");
		HashMap<String, String> h2 = new HashMap<String, String>();
		h2.put("2", "1");
		h2.put("1", "2");
		
		assertEquals(h1, h2);
	}

}
