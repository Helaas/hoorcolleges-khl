package
{
	import flash.events.Event;
	public class HoofdNietGekliktEvent extends Event
	{
			public function HoofdNietGekliktEvent(type:String, bubbles:Boolean = false, cancelable:Boolean = false){
				super(type, bubbles,cancelable);
			}
	}
}