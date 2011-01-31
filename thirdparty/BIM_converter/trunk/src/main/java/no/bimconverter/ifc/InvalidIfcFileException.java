package no.bimconverter.ifc;
/*
 * Uc = Unchecked... should probably change this
 */
public class InvalidIfcFileException extends RuntimeException {

	/**
	 * 
	 */
	private static final long serialVersionUID = -7670769841469006165L;
	
	public InvalidIfcFileException() {
		super();
	}
	public InvalidIfcFileException(String exception) {
		super(exception);
	}
	public InvalidIfcFileException(String exception, Throwable t) {
		super(exception, t);
	}
	
}
