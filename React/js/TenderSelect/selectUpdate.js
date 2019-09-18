class CashClass{
	constructor() {
        this.selectData  = WINDOWS_SELECT;        
    }	
	
	getConfig(){
	    return {
	    	selectData : this.selectData 
	    }    	
	}
	
	
	getData(){
		return{				
			selectData : this.selectData
	    } 
	}
}
var cash = new CashClass();
export default cash;