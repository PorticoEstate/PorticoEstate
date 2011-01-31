package no.bimconverter.ifc.v2x3;


import static junit.framework.Assert.assertNotNull;
import static org.junit.Assert.assertEquals;

import java.util.List;

import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBException;
import javax.xml.bind.Marshaller;



import no.bimconverter.ifc.Repositories;
import no.bimconverter.ifc.RepositoriesImpl;
import no.bimconverter.ifc.v2x3.object.element.Covering;

import org.junit.After;
import org.junit.Before;
import org.junit.Test;

public class WholeModelOutputTest {

	//String testingRepository = "ModelTestRepository";
	String testingRepository = "FMHandoverRepository";
	String nonExistingRepository = "dummmmmyRepoThatDoesNotExist";
	//String testIfcFileName = "sample.ifc";
	String testIfcFileName = "20091007_Test_BasicFM-HandOver_01_valid.ifc";
	
	int numberOfIfcElements = 1420;
	int numberOfIfcModels = 0;
	String ifcFilename = null;
	private IfcModelImpl model;
	private WholeModelOutput wholeModel= new WholeModelOutput();
	Repositories repo = null;
	private List<Covering> coverings;
	
	
	@Before
	public void setUp() {
		model = new IfcModelImpl(testingRepository);
		ifcFilename = (Thread.currentThread().getContextClassLoader().getResource(testIfcFileName)).toString();
		ifcFilename = ifcFilename.replace("file:/", "");
		repo = new RepositoriesImpl();
		System.out.println(ifcFilename);
		repo.addRepository(testingRepository, ifcFilename);
		
		wholeModel.load(model);
	}
	@After
	public void tearDown() {
		repo.deleteRepository(testingRepository);
	}
	
	@Test
	public void testObject() throws JAXBException { 
		assertNotNull(wholeModel);
	}
	@Test
	public void testDisplayCovering1() throws JAXBException {
		JAXBContext jc = JAXBContext.newInstance(WholeModelOutput.class);
		Marshaller m = jc.createMarshaller();
		m.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, true);
		m.marshal(wholeModel, System.out );
	}

}
