import Navbar from '@/components/navbar/navbar';
import { Button } from '@/components/ui/button';
import { TextEffect } from '@/components/ui/text-effect';

export default function LandingPage() {
    return (
        <div>
            <Navbar />
            <div className="h-[200vh]">
                <section className="relative flex h-screen justify-center bg-[url('/assets/what-up/background.png')] bg-cover bg-center">
                    <div className="absolute top-1/5 text-center font-bowlby text-primary text-stroke">
                        <TextEffect className="text-8xl" as='h1' preset='slide'>Convivial Futures</TextEffect>
                        <TextEffect className="text-3xl" as='h2' preset='slide'>(Not) a Multidimensional Ninjas' Hideout</TextEffect>
                    </div>
                    <Button className="font- absolute bottom-[4%] border-2 border-black px-20 font-bold text-black" size={'lg'}>
                        Wth Is This?
                    </Button>
                </section>
            </div>
        </div>
    );
}
