import { Button } from '@/components/ui/button';
import { TextEffect } from '@/components/ui/text-effect';
import MainLayout from '@/layouts/main-layout';

export default function LandingPage() {
    return (
        <MainLayout>
            <div className="h-[150vh]">
                <section className="relative flex h-screen justify-center bg-[url('/assets/what-up/background.png')] bg-cover bg-center">
                    <div className="absolute top-1/5 text-center font-bowlby text-primary text-stroke text-stroke-fill-primary">
                        <TextEffect className="text-7xl md:text-8xl" as="h1" preset="slide" delay={0.5}>
                            Convivial Futures
                        </TextEffect>
                        <TextEffect className="text-xl md:text-3xl" as="h2" preset="slide" delay={0.5}>
                            (Not) a Multidimensional Ninjas' Hideout
                        </TextEffect>
                    </div>
                    <Button className="absolute bottom-10 font-bold min-w-60" size={'lg'}>
                        Wth Is This?
                    </Button>
                </section>
            </div>
        </MainLayout>
    );
}
