package no.bimconverter.ifc.v2x3.object;

import static junit.framework.Assert.*;

import java.util.HashMap;
import java.util.List;

import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBException;
import javax.xml.bind.Marshaller;

import jsdai.lang.EEntity;
import jsdai.lang.SdaiException;



import no.bimconverter.ifc.Repositories;
import no.bimconverter.ifc.RepositoriesImpl;
import no.bimconverter.ifc.jaxb.owner.OwnerHistory;
import no.bimconverter.ifc.jaxb.owner.Person;
import no.bimconverter.ifc.jaxb.owner.PersonAndOrganization;
import no.bimconverter.ifc.v2x3.IfcModelImpl;

import org.junit.After;
import org.junit.Before;
import org.junit.Test;

public class ProjectTest {
	
	//String testingRepository = "ModelTestRepository";
	String testingRepository = "FMHandoverRepository";
	String nonExistingRepository = "dummmmmyRepoThatDoesNotExist";
	//String testIfcFileName = "sample.ifc";
	String testIfcFileName = "20091007_Test_BasicFM-HandOver_01_valid.ifc";
	
	int numberOfIfcElements = 1420;
	int numberOfIfcModels = 0;
	String ifcFilename = null;
	private IfcModelImpl model;
	private Project project;
	Repositories repo = null;
	
	@Before
	public void setUp() {
		model = new IfcModelImpl(testingRepository);
		ifcFilename = (Thread.currentThread().getContextClassLoader().getResource(testIfcFileName)).toString();
		ifcFilename = ifcFilename.replace("file:/", "");
		repo = new RepositoriesImpl();
		repo.addRepository(testingRepository, ifcFilename);
		project = model.getProject();
	}
	@After
	public void tearDown() {
		repo.deleteRepository(testingRepository);
	}
	
	@Test
	public void testProjectObject() throws JAXBException {
		
		JAXBContext jc = JAXBContext.newInstance(Project.class);
		Marshaller m = jc.createMarshaller();
		 m.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, true);
		m.marshal( project, System.out );
		assertNotNull(project);
	}
	
	@Test
	public void testOwnerHistory() throws JAXBException {
		OwnerHistory ownerHistory = project.getOwnerHistory();
		PersonAndOrganization owningUser = ownerHistory.getOwningUser();
		Person thePerson = owningUser.getPerson();
		JAXBContext jc = JAXBContext.newInstance(OwnerHistory.class);
		Marshaller m = jc.createMarshaller();
		 m.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, true);
		m.marshal( ownerHistory, System.out );
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
